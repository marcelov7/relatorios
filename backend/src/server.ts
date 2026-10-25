import 'dotenv/config';
import express from 'express';
import swaggerUi from 'swagger-ui-express';
import { SERVER } from './config/env';
import { routes } from './config/routes';
import { configureMiddleware } from './config/middleware';
import { configureSecurity } from './config/security';
import { logger } from './config/logger';
import { prisma } from './config/database';
import { swaggerSpec } from './config/docs';

// Criação da aplicação Express
const app = express();

// Configuração dos middlewares
configureMiddleware(app);

// Configuração de segurança
configureSecurity(app);

// Configuração da documentação
app.use('/docs', swaggerUi.serve, swaggerUi.setup(swaggerSpec));
app.get('/docs.json', (req, res) => {
  res.setHeader('Content-Type', 'application/json');
  res.send(swaggerSpec);
});

// Configuração das rotas
app.use('/api', routes);

// Inicialização do servidor
const startServer = async () => {
  try {
    // Testa a conexão com o banco de dados
    await prisma.$connect();
    logger.info('✅ Conectado ao banco de dados PostgreSQL');

    // Inicia o servidor
    app.listen(SERVER.PORT, () => {
      logger.info(`✅ Servidor rodando na porta ${SERVER.PORT} em modo ${SERVER.NODE_ENV}`);
      logger.info(`📚 Documentação disponível em ${SERVER.API_URL}/docs`);
    });
  } catch (error) {
    logger.error('❌ Erro ao iniciar o servidor:', error);
    process.exit(1);
  }
};

// Tratamento de erros não capturados
process.on('unhandledRejection', (error) => {
  logger.error('❌ Erro não tratado:', error);
  process.exit(1);
});

process.on('uncaughtException', (error) => {
  logger.error('❌ Exceção não capturada:', error);
  process.exit(1);
});

// Inicia o servidor
startServer();