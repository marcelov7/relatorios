import request from 'supertest';
import { app } from '../server';
import { prisma } from '../config/database';
import { generateToken } from '../config/auth';
import { setupTestEnvironment, teardownTestEnvironment, testUsers } from '../config/test';

describe('Relatorio API Tests', () => {
  let adminToken: string;
  let userToken: string;
  let userId: string;
  let setorId: string;
  let equipamentoId: string;

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
      userId = user.id;
    }

    // Obter IDs necessários para testes
    const setor = await prisma.setor.findFirst();
    const equipamento = await prisma.equipamento.findFirst();
    
    if (setor && equipamento) {
      setorId = setor.id;
      equipamentoId = equipamento.id;
    }
  });

  // Limpar ambiente após todos os testes
  afterAll(async () => {
    await teardownTestEnvironment();
  });

  describe('GET /api/relatorios', () => {
    it('should return 401 without token', async () => {
      const response = await request(app).get('/api/relatorios');
      expect(response.status).toBe(401);
    });

    it('should return all relatorios for admin', async () => {
      const response = await request(app)
        .get('/api/relatorios')
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(Array.isArray(response.body.data)).toBe(true);
    });

    it('should return only user relatorios for regular user', async () => {
      const response = await request(app)
        .get('/api/relatorios')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);
      expect(response.body.data.every((rel: any) => rel.userId === userId)).toBe(true);
    });

    it('should filter relatorios by status', async () => {
      const response = await request(app)
        .get('/api/relatorios?status=PENDENTE')
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body.data.every((rel: any) => rel.status === 'PENDENTE')).toBe(true);
    });

    it('should filter relatorios by prioridade', async () => {
      const response = await request(app)
        .get('/api/relatorios?prioridade=ALTA')
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body.data.every((rel: any) => rel.prioridade === 'ALTA')).toBe(true);
    });
  });

  describe('DELETE /api/relatorios/:id', () => {
    let relatorioId: string;

    beforeAll(async () => {
      // Criar um relatório para testes de deleção
      const relatorio = await prisma.relatorio.create({
        data: {
          titulo: 'Relatório para Deleção',
          descricao: 'Será deletado nos testes',
          status: 'PENDENTE',
          prioridade: 'BAIXA',
          userId,
          equipamentoId,
          setorId
        }
      });
      relatorioId = relatorio.id;
    });

    it('should delete own relatorio', async () => {
      const response = await request(app)
        .delete(`/api/relatorios/${relatorioId}`)
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);

      // Verificar se foi realmente deletado
      const deletedRelatorio = await prisma.relatorio.findUnique({
        where: { id: relatorioId }
      });
      expect(deletedRelatorio).toBeNull();
    });

    it('should delete any relatorio as admin', async () => {
      // Criar relatório com outro usuário
      const outroUser = await prisma.user.findUnique({
        where: { email: testUsers.manager.email }
      });

      if (outroUser) {
        const relatorio = await prisma.relatorio.create({
          data: {
            titulo: 'Relatório para Deleção pelo Admin',
            descricao: 'Teste de permissão admin',
            status: 'PENDENTE',
            prioridade: 'BAIXA',
            userId: outroUser.id,
            equipamentoId,
            setorId
          }
        });

        const response = await request(app)
          .delete(`/api/relatorios/${relatorio.id}`)
          .set('Authorization', `Bearer ${adminToken}`);

        expect(response.status).toBe(200);
      }
    });

    it('should not delete relatorio of another user', async () => {
      // Criar relatório com outro usuário
      const outroUser = await prisma.user.findUnique({
        where: { email: testUsers.manager.email }
      });

      if (outroUser) {
        const relatorio = await prisma.relatorio.create({
          data: {
            titulo: 'Relatório de Outro Usuário',
            descricao: 'Teste de deleção não autorizada',
            status: 'PENDENTE',
            prioridade: 'BAIXA',
            userId: outroUser.id,
            equipamentoId,
            setorId
          }
        });

        const response = await request(app)
          .delete(`/api/relatorios/${relatorio.id}`)
          .set('Authorization', `Bearer ${userToken}`);

        expect(response.status).toBe(403);
      }
    });
  });

  describe('GET /api/relatorios/dashboard', () => {
    it('should return dashboard data for admin', async () => {
      const response = await request(app)
        .get('/api/relatorios/dashboard')
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body.data).toHaveProperty('totalRelatorios');
      expect(response.body.data).toHaveProperty('relatoriosPorStatus');
      expect(response.body.data).toHaveProperty('relatoriosPorPrioridade');
      expect(response.body.data).toHaveProperty('relatoriosPorSetor');
    });

    it('should return filtered dashboard data for user', async () => {
      const response = await request(app)
        .get('/api/relatorios/dashboard')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);
      expect(response.body.data.relatoriosPorStatus).toBeDefined();
      // Verificar se os dados são apenas do usuário
      expect(response.body.data.totalRelatorios).toBeLessThanOrEqual(
        response.body.data.relatoriosPorStatus.reduce((acc: number, curr: any) => acc + curr.count, 0)
      );
    });
  });

  describe('GET /api/relatorios/recent', () => {
    it('should return recent activities for admin', async () => {
      const response = await request(app)
        .get('/api/relatorios/recent')
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(Array.isArray(response.body.data)).toBe(true);
      expect(response.body.data.length).toBeLessThanOrEqual(10); // Limite padrão
    });

    it('should return filtered recent activities for user', async () => {
      const response = await request(app)
        .get('/api/relatorios/recent')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);
      expect(Array.isArray(response.body.data)).toBe(true);
      // Verificar se todos os relatórios pertencem ao usuário
      expect(response.body.data.every((rel: any) => rel.userId === userId)).toBe(true);
    });

    it('should limit results based on query parameter', async () => {
      const limit = 5;
      const response = await request(app)
        .get(`/api/relatorios/recent?limit=${limit}`)
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body.data.length).toBeLessThanOrEqual(limit);
    });
  });
});

  describe('POST /api/relatorios', () => {
    const newRelatorio = {
      titulo: 'Relatório de Teste',
      descricao: 'Descrição do relatório de teste',
      status: 'PENDENTE',
      prioridade: 'MEDIA',
      equipamentoId: '',  // Será preenchido no beforeAll
      setorId: '',        // Será preenchido no beforeAll
      observacoes: 'Observações iniciais do teste'
    };

    beforeAll(() => {
      newRelatorio.equipamentoId = equipamentoId;
      newRelatorio.setorId = setorId;
    });

    it('should create new relatorio', async () => {
      const response = await request(app)
        .post('/api/relatorios')
        .set('Authorization', `Bearer ${userToken}`)
        .send(newRelatorio);

      expect(response.status).toBe(201);
      expect(response.body.data).toHaveProperty('id');
      expect(response.body.data.titulo).toBe(newRelatorio.titulo);
      expect(response.body.data.userId).toBe(userId);
    });

    it('should validate required fields', async () => {
      const response = await request(app)
        .post('/api/relatorios')
        .set('Authorization', `Bearer ${userToken}`)
        .send({});

      expect(response.status).toBe(400);
      expect(response.body).toHaveProperty('errors');
    });

    it('should validate status enum values', async () => {
      const response = await request(app)
        .post('/api/relatorios')
        .set('Authorization', `Bearer ${userToken}`)
        .send({
          ...newRelatorio,
          status: 'INVALID_STATUS'
        });

      expect(response.status).toBe(400);
    });

    it('should validate prioridade enum values', async () => {
      const response = await request(app)
        .post('/api/relatorios')
        .set('Authorization', `Bearer ${userToken}`)
        .send({
          ...newRelatorio,
          prioridade: 'INVALID_PRIORITY'
        });

      expect(response.status).toBe(400);
    });
  });

  describe('PUT /api/relatorios/:id', () => {
    let relatorioId: string;

    beforeAll(async () => {
      // Criar um relatório para testes de atualização
      const relatorio = await prisma.relatorio.create({
        data: {
          titulo: 'Relatório para Atualização',
          descricao: 'Descrição inicial',
          status: 'PENDENTE',
          prioridade: 'BAIXA',
          userId,
          equipamentoId,
          setorId
        }
      });
      relatorioId = relatorio.id;
    });

    it('should update relatorio by owner', async () => {
      const response = await request(app)
        .put(`/api/relatorios/${relatorioId}`)
        .set('Authorization', `Bearer ${userToken}`)
        .send({
          titulo: 'Relatório Atualizado',
          descricao: 'Descrição atualizada',
          status: 'EM_ANDAMENTO'
        });

      expect(response.status).toBe(200);
      expect(response.body.data.titulo).toBe('Relatório Atualizado');
      expect(response.body.data.status).toBe('EM_ANDAMENTO');
    });

    it('should update any relatorio as admin', async () => {
      const response = await request(app)
        .put(`/api/relatorios/${relatorioId}`)
        .set('Authorization', `Bearer ${adminToken}`)
        .send({
          titulo: 'Atualizado pelo Admin',
          prioridade: 'ALTA'
        });

      expect(response.status).toBe(200);
      expect(response.body.data.titulo).toBe('Atualizado pelo Admin');
      expect(response.body.data.prioridade).toBe('ALTA');
    });

    it('should not update relatorio of another user', async () => {
      // Criar relatório com outro usuário
      const outroUser = await prisma.user.findUnique({
        where: { email: testUsers.manager.email }
      });

      if (outroUser) {
        const relatorio = await prisma.relatorio.create({
          data: {
            titulo: 'Relatório de Outro Usuário',
            descricao: 'Teste de permissão',
            status: 'PENDENTE',
            prioridade: 'BAIXA',
            userId: outroUser.id,
            equipamentoId,
            setorId
          }
        });

        const response = await request(app)
          .put(`/api/relatorios/${relatorio.id}`)
          .set('Authorization', `Bearer ${userToken}`)
          .send({
            titulo: 'Tentativa de Atualização'
          });

        expect(response.status).toBe(403);
      }
    });
  });