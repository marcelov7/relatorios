import request from 'supertest';
import { app } from '../server';
import { prisma } from '../config/database';
import { generateToken } from '../config/auth';
import { setupTestEnvironment, teardownTestEnvironment, testUsers } from '../config/test';

describe('User API Tests', () => {
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

  describe('GET /api/users', () => {
    it('should return 401 without token', async () => {
      const response = await request(app).get('/api/users');
      expect(response.status).toBe(401);
    });

    it('should return users list for admin', async () => {
      const response = await request(app)
        .get('/api/users')
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(Array.isArray(response.body.data)).toBe(true);
      expect(response.body.data.length).toBeGreaterThan(0);
    });

    it('should return 403 for regular user', async () => {
      const response = await request(app)
        .get('/api/users')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(403);
    });
  });

  describe('POST /api/users', () => {
    const newUser = {
      username: 'newuser',
      name: 'New User',
      email: 'newuser@test.com',
      password: 'newuser123',
      role: 'USER'
    };

    it('should create new user as admin', async () => {
      const response = await request(app)
        .post('/api/users')
        .set('Authorization', `Bearer ${adminToken}`)
        .send(newUser);

      expect(response.status).toBe(201);
      expect(response.body.data).toHaveProperty('id');
      expect(response.body.data.email).toBe(newUser.email);
    });

    it('should not allow duplicate email', async () => {
      const response = await request(app)
        .post('/api/users')
        .set('Authorization', `Bearer ${adminToken}`)
        .send(newUser);

      expect(response.status).toBe(409);
    });

    it('should validate required fields', async () => {
      const response = await request(app)
        .post('/api/users')
        .set('Authorization', `Bearer ${adminToken}`)
        .send({});

      expect(response.status).toBe(400);
      expect(response.body).toHaveProperty('errors');
    });
  });

  describe('PUT /api/users/:id', () => {
    it('should update user name', async () => {
      const user = await prisma.user.findUnique({
        where: { email: testUsers.user.email }
      });

      if (user) {
        const response = await request(app)
          .put(`/api/users/${user.id}`)
          .set('Authorization', `Bearer ${adminToken}`)
          .send({ name: 'Updated Name' });

        expect(response.status).toBe(200);
        expect(response.body.data.name).toBe('Updated Name');
      }
    });

    it('should not update with invalid id', async () => {
      const response = await request(app)
        .put('/api/users/999999')
        .set('Authorization', `Bearer ${adminToken}`)
        .send({ name: 'Updated Name' });

      expect(response.status).toBe(404);
    });
  });

  describe('DELETE /api/users/:id', () => {
    it('should delete user as admin', async () => {
      const user = await prisma.user.findUnique({
        where: { email: 'newuser@test.com' }
      });

      if (user) {
        const response = await request(app)
          .delete(`/api/users/${user.id}`)
          .set('Authorization', `Bearer ${adminToken}`);

        expect(response.status).toBe(200);

        // Verificar se usuário foi realmente deletado
        const deletedUser = await prisma.user.findUnique({
          where: { id: user.id }
        });
        expect(deletedUser).toBeNull();
      }
    });

    it('should not delete admin user', async () => {
      const admin = await prisma.user.findUnique({
        where: { email: testUsers.admin.email }
      });

      if (admin) {
        const response = await request(app)
          .delete(`/api/users/${admin.id}`)
          .set('Authorization', `Bearer ${adminToken}`);

        expect(response.status).toBe(403);
      }
    });
  });
});