import request from 'supertest';
import { app } from '../server';
import { prisma } from '../config/database';
import { generateToken } from '../config/auth';
import { setupTestEnvironment, teardownTestEnvironment, testUsers, testEquipamentos, testSetores } from '../config/test';

describe('Equipamento API Tests', () => {
  let adminToken: string;
  let userToken: string;
  let setorId: string;

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

    // Obter ID do setor para testes
    const setor = await prisma.setor.findFirst();
    if (setor) {
      setorId = setor.id;
    }
  });

  // Limpar ambiente após todos os testes
  afterAll(async () => {
    await teardownTestEnvironment();
  });

  describe('GET /api/equipamentos', () => {
    it('should return 401 without token', async () => {
      const response = await request(app).get('/api/equipamentos');
      expect(response.status).toBe(401);
    });

    it('should return equipamentos list for authenticated user', async () => {
      const response = await request(app)
        .get('/api/equipamentos')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);
      expect(Array.isArray(response.body.data)).toBe(true);
      expect(response.body.data.length).toBeGreaterThan(0);
    });

    it('should filter equipamentos by status', async () => {
      const response = await request(app)
        .get('/api/equipamentos?status=ATIVO')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);
      expect(response.body.data.every((equip: any) => equip.status === 'ATIVO')).toBe(true);
    });

    it('should filter equipamentos by setor', async () => {
      const response = await request(app)
        .get(`/api/equipamentos?setorId=${setorId}`)
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);
      expect(response.body.data.every((equip: any) => equip.setorId === setorId)).toBe(true);
    });
  });

  describe('POST /api/equipamentos', () => {
    const newEquipamento = {
      nome: 'Equipamento de Teste Novo',
      descricao: 'Descrição do equipamento de teste novo',
      numeroSerie: 'TEST-001',
      status: 'ATIVO',
      setorId: ''  // Será preenchido no beforeAll
    };

    beforeAll(() => {
      newEquipamento.setorId = setorId;
    });

    it('should create new equipamento as admin', async () => {
      const response = await request(app)
        .post('/api/equipamentos')
        .set('Authorization', `Bearer ${adminToken}`)
        .send(newEquipamento);

      expect(response.status).toBe(201);
      expect(response.body.data).toHaveProperty('id');
      expect(response.body.data.nome).toBe(newEquipamento.nome);
      expect(response.body.data.numeroSerie).toBe(newEquipamento.numeroSerie);
    });

    it('should not allow duplicate numeroSerie', async () => {
      const response = await request(app)
        .post('/api/equipamentos')
        .set('Authorization', `Bearer ${adminToken}`)
        .send(newEquipamento);

      expect(response.status).toBe(409);
    });

    it('should validate required fields', async () => {
      const response = await request(app)
        .post('/api/equipamentos')
        .set('Authorization', `Bearer ${adminToken}`)
        .send({});

      expect(response.status).toBe(400);
      expect(response.body).toHaveProperty('errors');
    });

    it('should validate setor exists', async () => {
      const response = await request(app)
        .post('/api/equipamentos')
        .set('Authorization', `Bearer ${adminToken}`)
        .send({
          ...newEquipamento,
          setorId: 'invalid-setor-id',
          numeroSerie: 'TEST-002'
        });

      expect(response.status).toBe(404);
      expect(response.body.error).toContain('Setor não encontrado');
    });
  });

  describe('PUT /api/equipamentos/:id', () => {
    let equipamentoId: string;

    beforeAll(async () => {
      const equipamento = await prisma.equipamento.findFirst();
      if (equipamento) {
        equipamentoId = equipamento.id;
      }
    });

    it('should update equipamento as admin', async () => {
      const response = await request(app)
        .put(`/api/equipamentos/${equipamentoId}`)
        .set('Authorization', `Bearer ${adminToken}`)
        .send({
          nome: 'Equipamento Atualizado',
          descricao: 'Descrição atualizada',
          status: 'MANUTENCAO'
        });

      expect(response.status).toBe(200);
      expect(response.body.data.nome).toBe('Equipamento Atualizado');
      expect(response.body.data.status).toBe('MANUTENCAO');
    });

    it('should not update with invalid id', async () => {
      const response = await request(app)
        .put('/api/equipamentos/invalid-id')
        .set('Authorization', `Bearer ${adminToken}`)
        .send({ nome: 'Equipamento Inválido' });

      expect(response.status).toBe(404);
    });

    it('should validate status enum values', async () => {
      const response = await request(app)
        .put(`/api/equipamentos/${equipamentoId}`)
        .set('Authorization', `Bearer ${adminToken}`)
        .send({ status: 'INVALID_STATUS' });

      expect(response.status).toBe(400);
    });
  });

  describe('DELETE /api/equipamentos/:id', () => {
    let equipamentoParaDeletar: any;

    beforeAll(async () => {
      // Criar um equipamento específico para teste de deleção
      equipamentoParaDeletar = await prisma.equipamento.create({
        data: {
          nome: 'Equipamento Para Deletar',
          numeroSerie: 'DELETE-TEST-001',
          status: 'INATIVO',
          setorId
        }
      });
    });

    it('should delete equipamento as admin', async () => {
      const response = await request(app)
        .delete(`/api/equipamentos/${equipamentoParaDeletar.id}`)
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);

      // Verificar se o equipamento foi realmente deletado
      const equipamentoDeletado = await prisma.equipamento.findUnique({
        where: { id: equipamentoParaDeletar.id }
      });
      expect(equipamentoDeletado).toBeNull();
    });

    it('should not delete equipamento with associated relatorios', async () => {
      // Criar equipamento e relatório associado
      const equipamento = await prisma.equipamento.create({
        data: {
          nome: 'Equipamento Com Relatório',
          numeroSerie: 'TEST-REL-001',
          status: 'ATIVO',
          setorId,
          relatorios: {
            create: {
              titulo: 'Relatório Teste',
              descricao: 'Relatório para teste de deleção',
              status: 'PENDENTE',
              prioridade: 'BAIXA',
              userId: (await prisma.user.findFirst())!.id,
              setorId
            }
          }
        }
      });

      const response = await request(app)
        .delete(`/api/equipamentos/${equipamento.id}`)
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(400);
      expect(response.body.error).toContain('relatórios associados');
    });

    it('should not allow deletion by regular user', async () => {
      // Criar outro equipamento para tentar deletar
      const equipamento = await prisma.equipamento.create({
        data: {
          nome: 'Equipamento Para Teste de Permissão',
          numeroSerie: 'DELETE-TEST-002',
          status: 'INATIVO',
          setorId
        }
      });

      const response = await request(app)
        .delete(`/api/equipamentos/${equipamento.id}`)
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(403);
    });
  });

  describe('GET /api/equipamentos/setor/:setorId', () => {
    it('should return equipamentos by setor', async () => {
      const response = await request(app)
        .get(`/api/equipamentos/setor/${setorId}`)
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);
      expect(Array.isArray(response.body.data)).toBe(true);
      expect(response.body.data.every((equip: any) => equip.setorId === setorId)).toBe(true);
    });

    it('should return 404 for invalid setor', async () => {
      const response = await request(app)
        .get('/api/equipamentos/setor/invalid-setor-id')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(404);
    });

    it('should not return descartado equipamentos', async () => {
      const response = await request(app)
        .get(`/api/equipamentos/setor/${setorId}`)
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);
      expect(response.body.data.every((equip: any) => equip.status !== 'DESCARTADO')).toBe(true);
    });
  });
});