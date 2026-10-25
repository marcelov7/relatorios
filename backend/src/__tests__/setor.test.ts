import request from 'supertest';
import { app } from '../server';
import { prisma } from '../config/database';
import { generateToken } from '../config/auth';
import { setupTestEnvironment, teardownTestEnvironment, testUsers, testSetores } from '../config/test';

describe('Setor API Tests', () => {
  let adminToken: string;
  let userToken: string;

  // Configurar ambiente de teste antes de todos os testes
  beforeAll(async () => {
    await setupTestEnvironment();
    
    // Gerar tokens para testes
    const admin = await prisma.user.findUnique({
      where: { email: testUsers.admin.email }
    });
    const user = await prisma.user.findUnique({
      where: { email: testUsers.user.email }
    });

    if (admin && user) {
      adminToken = generateToken({ id: admin.id, role: admin.role });
      userToken = generateToken({ id: user.id, role: user.role });
    }
  });

  // Limpar ambiente após todos os testes
  afterAll(async () => {
    await teardownTestEnvironment();
  });

  describe('GET /api/setores', () => {
    it('should return 401 without token', async () => {
      const response = await request(app).get('/api/setores');
      expect(response.status).toBe(401);
    });

    it('should return setores list for authenticated user', async () => {
      const response = await request(app)
        .get('/api/setores')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);
      expect(Array.isArray(response.body.data)).toBe(true);
      expect(response.body.data.length).toBeGreaterThan(0);
    });

    it('should return only active setores by default', async () => {
      const response = await request(app)
        .get('/api/setores')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);
      expect(response.body.data.every((setor: any) => setor.active)).toBe(true);
    });
  });

  describe('POST /api/setores', () => {
    const newSetor = {
      nome: 'Setor de Teste Novo',
      descricao: 'Descrição do setor de teste novo'
    };

    it('should create new setor as admin', async () => {
      const response = await request(app)
        .post('/api/setores')
        .set('Authorization', `Bearer ${adminToken}`)
        .send(newSetor);

      expect(response.status).toBe(201);
      expect(response.body.data).toHaveProperty('id');
      expect(response.body.data.nome).toBe(newSetor.nome);
    });

    it('should not allow duplicate nome', async () => {
      const response = await request(app)
        .post('/api/setores')
        .set('Authorization', `Bearer ${adminToken}`)
        .send(newSetor);

      expect(response.status).toBe(409);
    });

    it('should validate required fields', async () => {
      const response = await request(app)
        .post('/api/setores')
        .set('Authorization', `Bearer ${adminToken}`)
        .send({});

      expect(response.status).toBe(400);
      expect(response.body).toHaveProperty('errors');
    });

    it('should not allow creation by regular user', async () => {
      const response = await request(app)
        .post('/api/setores')
        .set('Authorization', `Bearer ${userToken}`)
        .send(newSetor);

      expect(response.status).toBe(403);
    });
  });

  describe('PUT /api/setores/:id', () => {
    let setorId: string;

    beforeAll(async () => {
      const setor = await prisma.setor.findFirst({
        where: { nome: testSetores[0].nome }
      });
      if (setor) {
        setorId = setor.id;
      }
    });

    it('should update setor name as admin', async () => {
      const response = await request(app)
        .put(`/api/setores/${setorId}`)
        .set('Authorization', `Bearer ${adminToken}`)
        .send({ nome: 'Setor Atualizado', descricao: 'Descrição atualizada' });

      expect(response.status).toBe(200);
      expect(response.body.data.nome).toBe('Setor Atualizado');
      expect(response.body.data.descricao).toBe('Descrição atualizada');
    });

    it('should not update with invalid id', async () => {
      const response = await request(app)
        .put('/api/setores/invalid-id')
        .set('Authorization', `Bearer ${adminToken}`)
        .send({ nome: 'Setor Inválido' });

      expect(response.status).toBe(404);
    });

    it('should not allow update by regular user', async () => {
      const response = await request(app)
        .put(`/api/setores/${setorId}`)
        .set('Authorization', `Bearer ${userToken}`)
        .send({ nome: 'Tentativa de Atualização' });

      expect(response.status).toBe(403);
    });
  });

  describe('DELETE /api/setores/:id', () => {
    let setorParaDeletar: any;

    beforeAll(async () => {
      // Criar um setor específico para teste de deleção
      setorParaDeletar = await prisma.setor.create({
        data: {
          nome: 'Setor Para Deletar',
          descricao: 'Este setor será deletado nos testes'
        }
      });
    });

    it('should soft delete setor as admin', async () => {
      const response = await request(app)
        .delete(`/api/setores/${setorParaDeletar.id}`)
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);

      // Verificar se o setor foi marcado como inativo
      const setorDeletado = await prisma.setor.findUnique({
        where: { id: setorParaDeletar.id }
      });
      expect(setorDeletado?.active).toBe(false);
    });

    it('should not delete setor with associated equipamentos', async () => {
      // Criar setor e equipamento associado
      const setor = await prisma.setor.create({
        data: {
          nome: 'Setor Com Equipamento',
          descricao: 'Setor que não pode ser deletado',
          equipamentos: {
            create: {
              nome: 'Equipamento Teste',
              numeroSerie: 'TEST123',
              status: 'ATIVO'
            }
          }
        }
      });

      const response = await request(app)
        .delete(`/api/setores/${setor.id}`)
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(400);
      expect(response.body.error).toContain('equipamentos associados');
    });

    it('should not allow deletion by regular user', async () => {
      const response = await request(app)
        .delete(`/api/setores/${setorParaDeletar.id}`)
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(403);
    });
  });
});