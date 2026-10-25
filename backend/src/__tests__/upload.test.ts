import request from 'supertest';
import { app } from '../app';
import { prisma } from '../config/database';
import { generateToken } from '../utils/auth';
import { setupTestEnvironment, teardownTestEnvironment, testUsers } from '../config/test';
import fs from 'fs';
import path from 'path';
import { UPLOAD } from '../config/constants';

describe('File Upload System', () => {
  let userToken: string;
  let adminToken: string;
  const uploadDir = path.join(__dirname, '..', '..', UPLOAD.PATH);
  const testFilePath = path.join(__dirname, 'test-files');

  beforeAll(async () => {
    await setupTestEnvironment();

    // Configurar tokens de teste
    const admin = await prisma.user.findUnique({
      where: { email: testUsers.admin.email }
    });
    const user = await prisma.user.findUnique({
      where: { email: testUsers.user.email }
    });

    if (!admin || !user) {
      throw new Error('Erro ao configurar usuários para teste');
    }

    adminToken = generateToken(admin);
    userToken = generateToken(user);

    // Criar diretórios necessários
    if (!fs.existsSync(uploadDir)) {
      fs.mkdirSync(uploadDir, { recursive: true });
    }
    if (!fs.existsSync(testFilePath)) {
      fs.mkdirSync(testFilePath, { recursive: true });
    }

    // Criar arquivos de teste
    fs.writeFileSync(path.join(testFilePath, 'test.txt'), 'Conteúdo de teste');
    fs.writeFileSync(path.join(testFilePath, 'test.pdf'), Buffer.alloc(1024));
    fs.writeFileSync(path.join(testFilePath, 'test.jpg'), Buffer.alloc(1024));
  });

  afterAll(async () => {
    await teardownTestEnvironment();

    // Limpar arquivos de teste
    if (fs.existsSync(testFilePath)) {
      fs.rmSync(testFilePath, { recursive: true, force: true });
    }
  });

  afterEach(() => {
    // Limpar uploads após cada teste
    const files = fs.readdirSync(uploadDir);
    files.forEach(file => {
      fs.unlinkSync(path.join(uploadDir, file));
    });
  });

  describe('Single File Upload', () => {
    it('should upload text file successfully', async () => {
      const response = await request(app)
        .post('/api/upload')
        .set('Authorization', `Bearer ${userToken}`)
        .attach('file', path.join(testFilePath, 'test.txt'));

      expect(response.status).toBe(200);
      expect(response.body.data).toHaveProperty('filename');
      expect(response.body.data).toHaveProperty('path');
      expect(response.body.data).toHaveProperty('size');
    });

    it('should upload PDF file successfully', async () => {
      const response = await request(app)
        .post('/api/upload')
        .set('Authorization', `Bearer ${userToken}`)
        .attach('file', path.join(testFilePath, 'test.pdf'));

      expect(response.status).toBe(200);
      expect(response.body.data.mimetype).toBe('application/pdf');
    });

    it('should upload image file successfully', async () => {
      const response = await request(app)
        .post('/api/upload')
        .set('Authorization', `Bearer ${userToken}`)
        .attach('file', path.join(testFilePath, 'test.jpg'));

      expect(response.status).toBe(200);
      expect(response.body.data.mimetype).toMatch(/^image\//);
    });

    it('should require authentication', async () => {
      const response = await request(app)
        .post('/api/upload')
        .attach('file', path.join(testFilePath, 'test.txt'));

      expect(response.status).toBe(401);
    });
  });

  describe('Multiple File Upload', () => {
    it('should upload multiple files', async () => {
      const response = await request(app)
        .post('/api/upload/multiple')
        .set('Authorization', `Bearer ${userToken}`)
        .attach('files', path.join(testFilePath, 'test.txt'))
        .attach('files', path.join(testFilePath, 'test.pdf'));

      expect(response.status).toBe(200);
      expect(response.body.data).toHaveLength(2);
      expect(response.body.data[0]).toHaveProperty('filename');
      expect(response.body.data[1]).toHaveProperty('filename');
    });

    it('should validate maximum number of files', async () => {
      const maxFiles = 5;
      const files = Array(maxFiles + 1).fill(path.join(testFilePath, 'test.txt'));
      
      const req = request(app)
        .post('/api/upload/multiple')
        .set('Authorization', `Bearer ${userToken}`);
      
      files.forEach(file => {
        req.attach('files', file);
      });

      const response = await req;
      expect(response.status).toBe(400);
    });
  });

  describe('File Validation', () => {
    it('should validate file size', async () => {
      // Criar arquivo grande
      const largeFile = path.join(testFilePath, 'large.txt');
      fs.writeFileSync(largeFile, Buffer.alloc(UPLOAD.MAX_SIZE + 1024));

      const response = await request(app)
        .post('/api/upload')
        .set('Authorization', `Bearer ${userToken}`)
        .attach('file', largeFile);

      expect(response.status).toBe(400);
      expect(response.body).toHaveProperty('message');
    });

    it('should validate file type', async () => {
      // Criar arquivo com extensão não permitida
      const invalidFile = path.join(testFilePath, 'test.exe');
      fs.writeFileSync(invalidFile, 'conteúdo');

      const response = await request(app)
        .post('/api/upload')
        .set('Authorization', `Bearer ${userToken}`)
        .attach('file', invalidFile);

      expect(response.status).toBe(400);
      expect(response.body).toHaveProperty('message');
    });

    it('should sanitize filenames', async () => {
      // Criar arquivo com nome malicioso
      const maliciousFile = path.join(testFilePath, '../../../test.txt');
      fs.writeFileSync(maliciousFile, 'conteúdo');

      const response = await request(app)
        .post('/api/upload')
        .set('Authorization', `Bearer ${userToken}`)
        .attach('file', maliciousFile);

      expect(response.status).toBe(200);
      expect(response.body.data.filename).not.toContain('..');
    });
  });

  describe('File Management', () => {
    it('should list uploaded files', async () => {
      // Fazer upload primeiro
      await request(app)
        .post('/api/upload')
        .set('Authorization', `Bearer ${userToken}`)
        .attach('file', path.join(testFilePath, 'test.txt'));

      const response = await request(app)
        .get('/api/upload/files')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);
      expect(Array.isArray(response.body.data)).toBe(true);
      expect(response.body.data.length).toBeGreaterThan(0);
    });

    it('should delete file', async () => {
      // Fazer upload primeiro
      const uploadResponse = await request(app)
        .post('/api/upload')
        .set('Authorization', `Bearer ${userToken}`)
        .attach('file', path.join(testFilePath, 'test.txt'));

      const fileId = uploadResponse.body.data.id;

      const deleteResponse = await request(app)
        .delete(`/api/upload/files/${fileId}`)
        .set('Authorization', `Bearer ${userToken}`);

      expect(deleteResponse.status).toBe(200);

      // Verificar se arquivo foi realmente deletado
      const checkResponse = await request(app)
        .get(`/api/upload/files/${fileId}`)
        .set('Authorization', `Bearer ${userToken}`);

      expect(checkResponse.status).toBe(404);
    });

    it('should not allow deleting files from other users', async () => {
      // Upload com usuário 1
      const uploadResponse = await request(app)
        .post('/api/upload')
        .set('Authorization', `Bearer ${userToken}`)
        .attach('file', path.join(testFilePath, 'test.txt'));

      const fileId = uploadResponse.body.data.id;

      // Tentar deletar com admin
      const deleteResponse = await request(app)
        .delete(`/api/upload/files/${fileId}`)
        .set('Authorization', `Bearer ${adminToken}`);

      expect(deleteResponse.status).toBe(403);
    });
  });

  describe('Error Handling', () => {
    it('should handle missing file', async () => {
      const response = await request(app)
        .post('/api/upload')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(400);
      expect(response.body).toHaveProperty('message');
    });

    it('should handle upload errors', async () => {
      // Remover permissões do diretório de upload
      fs.chmodSync(uploadDir, 0o444);

      const response = await request(app)
        .post('/api/upload')
        .set('Authorization', `Bearer ${userToken}`)
        .attach('file', path.join(testFilePath, 'test.txt'));

      expect(response.status).toBe(500);
      expect(response.body).toHaveProperty('message');

      // Restaurar permissões
      fs.chmodSync(uploadDir, 0o777);
    });

    it('should handle concurrent uploads', async () => {
      const promises = Array.from({ length: 10 }, () => {
        return request(app)
          .post('/api/upload')
          .set('Authorization', `Bearer ${userToken}`)
          .attach('file', path.join(testFilePath, 'test.txt'));
      });

      const responses = await Promise.all(promises);
      responses.forEach(response => {
        expect(response.status).toBe(200);
      });
    });
  });
});