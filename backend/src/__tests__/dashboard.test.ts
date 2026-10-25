import request from 'supertest';
import { app } from '../app';
import { prisma } from '../config/database';
import { generateToken } from '../utils/auth';
import { setupTestEnvironment, teardownTestEnvironment, testUsers } from '../config/test';

describe('Dashboard API', () => {
  let adminToken: string;
  let userToken: string;
  let userId: string;

  beforeAll(async () => {
    await setupTestEnvironment();

    // Obter usuários e gerar tokens
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
    userId = user.id;
  });

  afterAll(async () => {
    await teardownTestEnvironment();
  });

  describe('GET /api/dashboard/summary', () => {
    it('should return complete summary for admin', async () => {
      const response = await request(app)
        .get('/api/dashboard/summary')
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body.data).toHaveProperty('totalRelatorios');
      expect(response.body.data).toHaveProperty('totalEquipamentos');
      expect(response.body.data).toHaveProperty('totalSetores');
      expect(response.body.data).toHaveProperty('relatoriosRecentes');
      expect(response.body.data).toHaveProperty('equipamentosPorStatus');
      expect(response.body.data).toHaveProperty('relatoriosPorPrioridade');
    });

    it('should return filtered summary for user', async () => {
      const response = await request(app)
        .get('/api/dashboard/summary')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);
      expect(response.body.data.relatoriosRecentes.every((rel: any) => rel.userId === userId)).toBe(true);
    });

    it('should require authentication', async () => {
      const response = await request(app)
        .get('/api/dashboard/summary');

      expect(response.status).toBe(401);
    });
  });

  describe('GET /api/dashboard/stats', () => {
    it('should return statistics for admin', async () => {
      const response = await request(app)
        .get('/api/dashboard/stats')
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body.data).toHaveProperty('relatoriosPorMes');
      expect(response.body.data).toHaveProperty('equipamentosPorSetor');
      expect(response.body.data).toHaveProperty('relatoriosPorStatus');
    });

    it('should return filtered statistics for user', async () => {
      const response = await request(app)
        .get('/api/dashboard/stats')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);
      // Verificar se os dados são apenas do usuário
      expect(response.body.data.relatoriosPorMes.every((item: any) => 
        item.relatorios.every((rel: any) => rel.userId === userId)
      )).toBe(true);
    });

    it('should accept date range parameters', async () => {
      const startDate = new Date();
      startDate.setMonth(startDate.getMonth() - 1);
      
      const response = await request(app)
        .get('/api/dashboard/stats')
        .query({
          startDate: startDate.toISOString(),
          endDate: new Date().toISOString()
        })
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body.data.relatoriosPorMes.length).toBeLessThanOrEqual(2);
    });
  });

  describe('GET /api/dashboard/performance', () => {
    it('should return performance metrics for admin', async () => {
      const response = await request(app)
        .get('/api/dashboard/performance')
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body.data).toHaveProperty('tempoMedioResolucao');
      expect(response.body.data).toHaveProperty('taxaResolucao');
      expect(response.body.data).toHaveProperty('relatoriosPorTecnico');
    });

    it('should return filtered performance metrics for user', async () => {
      const response = await request(app)
        .get('/api/dashboard/performance')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);
      expect(response.body.data.relatoriosPorTecnico).toHaveLength(1);
      expect(response.body.data.relatoriosPorTecnico[0].userId).toBe(userId);
    });

    it('should accept period parameter', async () => {
      const response = await request(app)
        .get('/api/dashboard/performance')
        .query({ period: '7d' })
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body.data).toHaveProperty('periodo', '7d');
    });
  });
});