import { logger } from '../config/logger';
import fs from 'fs';
import path from 'path';

describe('Logger System', () => {
  const logDir = path.join(__dirname, '..', '..', 'logs');
  const testLogFile = path.join(logDir, 'test.log');

  beforeAll(() => {
    // Garantir que o diretório de logs existe
    if (!fs.existsSync(logDir)) {
      fs.mkdirSync(logDir, { recursive: true });
    }
  });

  beforeEach(() => {
    // Limpar arquivo de log antes de cada teste
    if (fs.existsSync(testLogFile)) {
      fs.writeFileSync(testLogFile, '');
    }
  });

  afterAll(() => {
    // Limpar arquivo de teste após todos os testes
    if (fs.existsSync(testLogFile)) {
      fs.unlinkSync(testLogFile);
    }
  });

  describe('Log Levels', () => {
    it('should log messages at different levels', () => {
      logger.error('Erro de teste');
      logger.warn('Aviso de teste');
      logger.info('Informação de teste');
      logger.debug('Debug de teste');

      const logContent = fs.readFileSync(testLogFile, 'utf8');
      
      expect(logContent).toContain('ERROR');
      expect(logContent).toContain('WARN');
      expect(logContent).toContain('INFO');
      expect(logContent).toContain('DEBUG');
    });

    it('should respect log level hierarchy', () => {
      // Configurar logger para nível INFO
      const originalLevel = logger.level;
      logger.level = 'info';

      logger.error('Erro de teste');
      logger.warn('Aviso de teste');
      logger.info('Informação de teste');
      logger.debug('Debug de teste');

      const logContent = fs.readFileSync(testLogFile, 'utf8');
      
      expect(logContent).toContain('ERROR');
      expect(logContent).toContain('WARN');
      expect(logContent).toContain('INFO');
      expect(logContent).not.toContain('DEBUG');

      // Restaurar nível original
      logger.level = originalLevel;
    });
  });

  describe('Log Format', () => {
    it('should include timestamp', () => {
      logger.info('Teste de timestamp');
      
      const logContent = fs.readFileSync(testLogFile, 'utf8');
      const timestampRegex = /\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/;
      
      expect(logContent).toMatch(timestampRegex);
    });

    it('should format objects correctly', () => {
      const testObject = {
        id: 1,
        name: 'Test',
        nested: { key: 'value' }
      };

      logger.info('Objeto de teste', testObject);
      
      const logContent = fs.readFileSync(testLogFile, 'utf8');
      
      expect(logContent).toContain('id');
      expect(logContent).toContain('name');
      expect(logContent).toContain('nested');
    });

    it('should handle error objects', () => {
      const error = new Error('Erro de teste');
      logger.error('Erro ocorrido', error);
      
      const logContent = fs.readFileSync(testLogFile, 'utf8');
      
      expect(logContent).toContain('Error: Erro de teste');
      expect(logContent).toContain('at ');  // Stack trace
    });
  });

  describe('Log Rotation', () => {
    it('should create new log file when size limit is reached', () => {
      // Gerar logs suficientes para forçar rotação
      const largeMessage = 'X'.repeat(1024); // 1KB
      for (let i = 0; i < 1024; i++) {  // Gerar ~1MB de logs
        logger.info(largeMessage);
      }

      const logFiles = fs.readdirSync(logDir)
        .filter(file => file.endsWith('.log'));
      
      expect(logFiles.length).toBeGreaterThan(1);
    });

    it('should maintain correct file naming pattern', () => {
      const logFiles = fs.readdirSync(logDir)
        .filter(file => file.endsWith('.log'));
      
      const filePattern = /^[\w-]+\.log(\.\d+)?$/;
      logFiles.forEach(file => {
        expect(file).toMatch(filePattern);
      });
    });
  });

  describe('Error Handling', () => {
    it('should handle circular references', () => {
      const circular: any = { key: 'value' };
      circular.self = circular;

      expect(() => {
        logger.info('Teste de referência circular', circular);
      }).not.toThrow();
    });

    it('should handle large objects', () => {
      const largeObject = {
        data: Array.from({ length: 1000 }, (_, i) => ({
          id: i,
          value: `test${i}`,
          nested: { more: 'data' }
        }))
      };

      expect(() => {
        logger.info('Objeto grande', largeObject);
      }).not.toThrow();
    });

    it('should handle write errors gracefully', () => {
      // Simular erro de escrita removendo permissões
      const tempFile = path.join(logDir, 'no-permission.log');
      fs.writeFileSync(tempFile, '');
      fs.chmodSync(tempFile, 0o444); // Somente leitura

      expect(() => {
        logger.info('Teste de erro de escrita');
      }).not.toThrow();

      // Limpar
      fs.unlinkSync(tempFile);
    });
  });

  describe('Performance', () => {
    it('should handle rapid logging', () => {
      console.time('rapid-logging');
      for (let i = 0; i < 1000; i++) {
        logger.info(`Log rápido ${i}`);
      }
      console.timeEnd('rapid-logging');

      const logContent = fs.readFileSync(testLogFile, 'utf8');
      expect(logContent).toContain('Log rápido 0');
      expect(logContent).toContain('Log rápido 999');
    });

    it('should handle concurrent logging', async () => {
      const promises = Array.from({ length: 100 }, (_, i) => {
        return new Promise<void>(resolve => {
          setTimeout(() => {
            logger.info(`Log concorrente ${i}`);
            resolve();
          }, Math.random() * 100);
        });
      });

      await Promise.all(promises);

      const logContent = fs.readFileSync(testLogFile, 'utf8');
      expect(logContent).toContain('Log concorrente 0');
      expect(logContent).toContain('Log concorrente 99');
    });
  });

  describe('Custom Formats', () => {
    it('should format HTTP requests correctly', () => {
      const req = {
        method: 'GET',
        url: '/api/test',
        headers: {
          'user-agent': 'test-agent'
        },
        ip: '127.0.0.1'
      };

      logger.info('Requisição HTTP', { req });
      
      const logContent = fs.readFileSync(testLogFile, 'utf8');
      
      expect(logContent).toContain('GET /api/test');
      expect(logContent).toContain('127.0.0.1');
    });

    it('should format database operations correctly', () => {
      const dbOp = {
        operation: 'INSERT',
        table: 'users',
        data: { id: 1, name: 'Test' }
      };

      logger.info('Operação de banco de dados', { db: dbOp });
      
      const logContent = fs.readFileSync(testLogFile, 'utf8');
      
      expect(logContent).toContain('INSERT');
      expect(logContent).toContain('users');
    });

    it('should mask sensitive data', () => {
      const sensitiveData = {
        email: 'test@example.com',
        password: 'secret123',
        token: 'abc123'
      };

      logger.info('Dados sensíveis', { sensitive: sensitiveData });
      
      const logContent = fs.readFileSync(testLogFile, 'utf8');
      
      expect(logContent).not.toContain('secret123');
      expect(logContent).not.toContain('abc123');
      expect(logContent).toContain('***');
    });
  });
});