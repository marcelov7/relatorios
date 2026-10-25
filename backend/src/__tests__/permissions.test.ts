import { PermissionService } from '../services/permission';
import { PrismaClient } from '@prisma/client';
import { generateToken } from '../utils/auth';
import { setupTestEnvironment, teardownTestEnvironment } from '../utils/test';
import supertest from 'supertest';
import app from '../app';

const prisma = new PrismaClient();
const request = supertest(app);

describe('Sistema de Permissões', () => {
  // Variáveis para armazenar tokens e serviço
  let adminToken: string;
  let userToken: string;
  let permissionService: PermissionService;

/**
 * Testes do Sistema de Permissões
 * 
 * Este arquivo contém testes para verificar:
 * - Permissões básicas (acesso admin vs usuário)
 * - Permissões de recursos (relatórios próprios vs de outros)
 * - Acesso baseado em função (gerenciamento de usuários)
 * - Verificações de permissão (checkPermission)
 * - Tratamento de erros (tokens inválidos/ausentes)
 */

  beforeAll(async () => {
    await setupTestEnvironment();
    adminToken = generateToken({ id: 1, role: 'ADMIN' });
    userToken = generateToken({ id: 2, role: 'USER' });
    permissionService = new PermissionService(prisma);
  });

  afterAll(async () => {
    await teardownTestEnvironment();
  });

  describe('Permissões Básicas', () => {
    it('deve permitir admin acessar rotas de admin', async () => {
      const response = await request
        .get('/api/admin/dashboard')
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('metrics');
      expect(response.body).toHaveProperty('recentReports');
    });

    it('deve negar acesso de usuário às rotas de admin', async () => {
      const response = await request
        .get('/api/admin/dashboard')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(403);
      expect(response.body).toHaveProperty('error', 'Acesso não autorizado');
    });

    it('deve permitir usuário acessar seus próprios dados', async () => {
      const response = await request
        .get('/api/users/2/profile')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('id', 2);
      expect(response.body).toHaveProperty('email', 'user@test.com');
    });

    it('deve negar acesso de usuário aos dados de outros usuários', async () => {
      const response = await request
        .get('/api/users/1/profile')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(403);
      expect(response.body).toHaveProperty('error', 'Acesso não autorizado');
    });
  });

  describe('Acesso Baseado em Função', () => {
    it('deve permitir admin gerenciar usuários', async () => {
      // Criar usuário
      const createResponse = await request
        .post('/api/users')
        .set('Authorization', `Bearer ${adminToken}`)
        .send({
          name: 'Novo Usuário',
          email: 'novo@teste.com',
          password: 'SenhaForte123!',
          role: 'USER'
        });

      expect(createResponse.status).toBe(201);
      expect(createResponse.body).toHaveProperty('id');
      expect(createResponse.body).toHaveProperty('email', 'novo@teste.com');

      // Atualizar usuário
      const updateResponse = await request
        .put(`/api/users/${createResponse.body.id}`)
        .set('Authorization', `Bearer ${adminToken}`)
        .send({
          name: 'Nome Atualizado',
          role: 'SUPERVISOR'
        });

      expect(updateResponse.status).toBe(200);
      expect(updateResponse.body).toHaveProperty('name', 'Nome Atualizado');
      expect(updateResponse.body).toHaveProperty('role', 'SUPERVISOR');

      // Deletar usuário
      const deleteResponse = await request
        .delete(`/api/users/${createResponse.body.id}`)
        .set('Authorization', `Bearer ${adminToken}`);

      expect(deleteResponse.status).toBe(200);
    });

    it('deve negar usuário de gerenciar usuários', async () => {
      // Tentar criar usuário
      const createResponse = await request
        .post('/api/users')
        .set('Authorization', `Bearer ${userToken}`)
        .send({
          name: 'Tentativa Usuário',
          email: 'tentativa@teste.com',
          password: 'SenhaForte123!',
          role: 'USER'
        });

      expect(createResponse.status).toBe(403);
      expect(createResponse.body).toHaveProperty('error', 'Acesso não autorizado');

      // Tentar atualizar usuário
      const updateResponse = await request
        .put('/api/users/1')
        .set('Authorization', `Bearer ${userToken}`)
        .send({
          name: 'Tentativa Atualização'
        });

      expect(updateResponse.status).toBe(403);
      expect(updateResponse.body).toHaveProperty('error', 'Acesso não autorizado');

      // Tentar deletar usuário
      const deleteResponse = await request
        .delete('/api/users/1')
        .set('Authorization', `Bearer ${userToken}`);

      expect(deleteResponse.status).toBe(403);
      expect(deleteResponse.body).toHaveProperty('error', 'Acesso não autorizado');
    });

    it('deve permitir admin ver todos os relatórios', async () => {
      const response = await request
        .get('/api/reports')
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(Array.isArray(response.body)).toBe(true);
      expect(response.body.length).toBeGreaterThan(0);
    });

    it('deve limitar usuário aos seus próprios relatórios', async () => {
      const response = await request
        .get('/api/reports')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);
      expect(Array.isArray(response.body)).toBe(true);
      response.body.forEach(report => {
        expect(report.userId).toBe(2);
      });
    });
  });

  describe('Verificações de Permissão', () => {
    let reportId: number;

    beforeEach(async () => {
      // Criar relatório para testes
      const createResponse = await request
        .post('/api/reports')
        .set('Authorization', `Bearer ${userToken}`)
        .send({
          title: 'Relatório para Verificação',
          description: 'Descrição do relatório',
          status: 'PENDING',
          priority: 'MEDIUM',
          equipmentId: 1
        });

      reportId = createResponse.body.id;
    });

    it('deve verificar permissão de usuário para recurso próprio', async () => {
      const hasPermission = await permissionService.checkPermission({
        userId: 2,
        action: 'READ',
        resource: 'REPORT',
        resourceId: reportId
      });

      expect(hasPermission).toBe(true);
    });

    it('deve verificar permissão de usuário para recurso de outro', async () => {
      const hasPermission = await permissionService.checkPermission({
        userId: 3,
        action: 'UPDATE',
        resource: 'REPORT',
        resourceId: reportId
      });

      expect(hasPermission).toBe(false);
    });

    it('deve verificar permissão de admin para qualquer recurso', async () => {
      const hasPermission = await permissionService.checkPermission({
        userId: 1,
        action: 'DELETE',
        resource: 'REPORT',
        resourceId: reportId
      });

      expect(hasPermission).toBe(true);
    });

    it('deve verificar permissão para ações específicas', async () => {
      // Usuário pode ler seu próprio relatório
      expect(await permissionService.checkPermission({
        userId: 2,
        action: 'READ',
        resource: 'REPORT',
        resourceId: reportId
      })).toBe(true);

      // Usuário pode atualizar seu próprio relatório
      expect(await permissionService.checkPermission({
        userId: 2,
        action: 'UPDATE',
        resource: 'REPORT',
        resourceId: reportId
      })).toBe(true);

      // Usuário não pode deletar seu próprio relatório
      expect(await permissionService.checkPermission({
        userId: 2,
        action: 'DELETE',
        resource: 'REPORT',
        resourceId: reportId
      })).toBe(false);
    });
  });

  describe('Tratamento de Erros', () => {
    it('deve tratar token ausente', async () => {
      const response = await request.get('/api/reports');

      expect(response.status).toBe(401);
      expect(response.body).toHaveProperty('error', 'Token de autenticação não fornecido');
    });

    it('deve tratar token inválido', async () => {
      const response = await request
        .get('/api/reports')
        .set('Authorization', 'Bearer token-invalido');

      expect(response.status).toBe(401);
      expect(response.body).toHaveProperty('error', 'Token de autenticação inválido');
    });

    it('deve tratar token expirado', async () => {
      // Gerar token expirado
      const expiredToken = generateToken({ 
        id: 2, 
        role: 'USER' 
      }, '0s'); // expira imediatamente

      const response = await request
        .get('/api/reports')
        .set('Authorization', `Bearer ${expiredToken}`);

      expect(response.status).toBe(401);
      expect(response.body).toHaveProperty('error', 'Token de autenticação expirado');
    });

    it('deve tratar permissão inválida', async () => {
      const response = await request
        .post('/api/admin/users')
        .set('Authorization', `Bearer ${userToken}`)
        .send({
          name: 'Teste',
          email: 'teste@teste.com',
          role: 'SUPER_ADMIN' // role inválida
        });

      expect(response.status).toBe(400);
      expect(response.body).toHaveProperty('error', 'Função de usuário inválida');
    });
  });