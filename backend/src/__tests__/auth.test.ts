import request from 'supertest';
import { app } from '../app';
import { prisma } from '../config/database';
import { generateToken } from '../utils/auth';
import { setupTestEnvironment, teardownTestEnvironment, testUsers } from '../config/test';
import bcrypt from 'bcryptjs';

describe('Authentication API', () => {
  beforeAll(async () => {
    await setupTestEnvironment();
  });

  afterAll(async () => {
    await teardownTestEnvironment();
  });

  describe('POST /api/auth/login', () => {
    it('should authenticate user with valid credentials', async () => {
      const response = await request(app)
        .post('/api/auth/login')
        .send({
          email: testUsers.user.email,
          password: testUsers.user.password
        });

      expect(response.status).toBe(200);
      expect(response.body.data).toHaveProperty('token');
      expect(response.body.data).toHaveProperty('user');
      expect(response.body.data.user).toHaveProperty('id');
      expect(response.body.data.user).toHaveProperty('email', testUsers.user.email);
      expect(response.body.data.user).toHaveProperty('role');
      expect(response.body.data.user).not.toHaveProperty('password');
    });

    it('should not authenticate with invalid email', async () => {
      const response = await request(app)
        .post('/api/auth/login')
        .send({
          email: 'invalid@email.com',
          password: testUsers.user.password
        });

      expect(response.status).toBe(401);
      expect(response.body).toHaveProperty('message');
    });

    it('should not authenticate with invalid password', async () => {
      const response = await request(app)
        .post('/api/auth/login')
        .send({
          email: testUsers.user.email,
          password: 'wrongpassword'
        });

      expect(response.status).toBe(401);
      expect(response.body).toHaveProperty('message');
    });

    it('should validate required fields', async () => {
      const response = await request(app)
        .post('/api/auth/login')
        .send({});

      expect(response.status).toBe(400);
      expect(response.body).toHaveProperty('errors');
    });
  });

  describe('POST /api/auth/register', () => {
    const newUser = {
      nome: 'Novo Usuário',
      email: 'novo@usuario.com',
      password: 'Senha@123',
      role: 'USER'
    };

    afterEach(async () => {
      // Limpar usuário criado
      await prisma.user.deleteMany({
        where: { email: newUser.email }
      });
    });

    it('should register new user', async () => {
      const response = await request(app)
        .post('/api/auth/register')
        .send(newUser);

      expect(response.status).toBe(201);
      expect(response.body.data).toHaveProperty('id');
      expect(response.body.data).toHaveProperty('email', newUser.email);
      expect(response.body.data).not.toHaveProperty('password');

      // Verificar se a senha foi hasheada
      const createdUser = await prisma.user.findUnique({
        where: { email: newUser.email }
      });
      expect(createdUser).toBeTruthy();
      if (createdUser) {
        expect(await bcrypt.compare(newUser.password, createdUser.password)).toBe(true);
      }
    });

    it('should not register user with existing email', async () => {
      // Primeiro registro
      await request(app)
        .post('/api/auth/register')
        .send(newUser);

      // Tentativa de registro duplicado
      const response = await request(app)
        .post('/api/auth/register')
        .send(newUser);

      expect(response.status).toBe(400);
      expect(response.body).toHaveProperty('message');
    });

    it('should validate password strength', async () => {
      const weakPasswords = [
        'abc123', // muito curta
        'abcdefgh', // sem números
        '12345678', // sem letras
        'Abc@123', // menos de 8 caracteres
      ];

      for (const password of weakPasswords) {
        const response = await request(app)
          .post('/api/auth/register')
          .send({
            ...newUser,
            password
          });

        expect(response.status).toBe(400);
        expect(response.body).toHaveProperty('errors');
      }
    });

    it('should validate email format', async () => {
      const response = await request(app)
        .post('/api/auth/register')
        .send({
          ...newUser,
          email: 'invalidemail'
        });

      expect(response.status).toBe(400);
      expect(response.body).toHaveProperty('errors');
    });
  });

  describe('POST /api/auth/forgot-password', () => {
    it('should send reset password email for valid user', async () => {
      const response = await request(app)
        .post('/api/auth/forgot-password')
        .send({
          email: testUsers.user.email
        });

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('message');
    });

    it('should handle non-existent email gracefully', async () => {
      const response = await request(app)
        .post('/api/auth/forgot-password')
        .send({
          email: 'nonexistent@email.com'
        });

      expect(response.status).toBe(200); // Mesma resposta por segurança
      expect(response.body).toHaveProperty('message');
    });
  });

  describe('POST /api/auth/reset-password', () => {
    let resetToken: string;
    let userId: string;

    beforeAll(async () => {
      // Criar token de reset
      const user = await prisma.user.findUnique({
        where: { email: testUsers.user.email }
      });
      if (!user) throw new Error('Usuário de teste não encontrado');
      
      userId = user.id;
      resetToken = generateToken(user, '1h');

      await prisma.user.update({
        where: { id: user.id },
        data: { resetPasswordToken: resetToken }
      });
    });

    it('should reset password with valid token', async () => {
      const newPassword = 'NovaSenha@123';
      
      const response = await request(app)
        .post('/api/auth/reset-password')
        .send({
          token: resetToken,
          password: newPassword
        });

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('message');

      // Verificar se a senha foi alterada
      const updatedUser = await prisma.user.findUnique({
        where: { id: userId }
      });
      expect(updatedUser).toBeTruthy();
      if (updatedUser) {
        expect(await bcrypt.compare(newPassword, updatedUser.password)).toBe(true);
      }
    });

    it('should not reset password with invalid token', async () => {
      const response = await request(app)
        .post('/api/auth/reset-password')
        .send({
          token: 'invalid-token',
          password: 'NovaSenha@123'
        });

      expect(response.status).toBe(400);
      expect(response.body).toHaveProperty('message');
    });

    it('should validate new password strength', async () => {
      const response = await request(app)
        .post('/api/auth/reset-password')
        .send({
          token: resetToken,
          password: 'weak'
        });

      expect(response.status).toBe(400);
      expect(response.body).toHaveProperty('errors');
    });
  });

  describe('GET /api/auth/me', () => {
    let userToken: string;

    beforeAll(async () => {
      const user = await prisma.user.findUnique({
        where: { email: testUsers.user.email }
      });
      if (!user) throw new Error('Usuário de teste não encontrado');
      userToken = generateToken(user);
    });

    it('should return authenticated user profile', async () => {
      const response = await request(app)
        .get('/api/auth/me')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);
      expect(response.body.data).toHaveProperty('id');
      expect(response.body.data).toHaveProperty('email');
      expect(response.body.data).not.toHaveProperty('password');
    });

    it('should require authentication', async () => {
      const response = await request(app)
        .get('/api/auth/me');

      expect(response.status).toBe(401);
    });

    it('should handle invalid token', async () => {
      const response = await request(app)
        .get('/api/auth/me')
        .set('Authorization', 'Bearer invalid-token');

      expect(response.status).toBe(401);
    });
  });
});