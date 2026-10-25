import request from 'supertest';
import express, { Request, Response, NextFunction } from 'express';
import { authenticate } from '../middleware/authenticate';
import { authorize } from '../middleware/auth';
import { errorHandler } from '../middleware/error';
import { notFound } from '../middleware/notFound';
import { generateToken } from '../utils/auth';
import { prisma } from '../config/database';
import { setupTestEnvironment, teardownTestEnvironment, testUsers } from '../config/test';

describe('Middleware Tests', () => {
  let app: express.Application;
  let adminToken: string;
  let userToken: string;

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

    // Configurar app de teste
    app = express();
    app.use(express.json());

    // Rota protegida por autenticação
    app.get('/protected', authenticate, (req: Request, res: Response) => {
      res.json({ message: 'Acesso permitido' });
    });

    // Rota protegida por autorização de admin
    app.get('/admin-only', authenticate, authorize(['ADMIN']), (req: Request, res: Response) => {
      res.json({ message: 'Acesso admin permitido' });
    });

    // Rota que gera erro
    app.get('/error', (req: Request, res: Response) => {
      throw new Error('Erro de teste');
    });

    // Configurar middlewares de erro
    app.use(notFound);
    app.use(errorHandler);
  });

  afterAll(async () => {
    await teardownTestEnvironment();
  });

  describe('authenticate Middleware', () => {
    it('should allow access with valid token', async () => {
      const response = await request(app)
        .get('/protected')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('message', 'Acesso permitido');
    });

    it('should deny access without token', async () => {
      const response = await request(app)
        .get('/protected');

      expect(response.status).toBe(401);
      expect(response.body).toHaveProperty('message');
    });

    it('should deny access with invalid token', async () => {
      const response = await request(app)
        .get('/protected')
        .set('Authorization', 'Bearer invalid-token');

      expect(response.status).toBe(401);
      expect(response.body).toHaveProperty('message');
    });

    it('should deny access with malformed token', async () => {
      const response = await request(app)
        .get('/protected')
        .set('Authorization', 'InvalidFormat');

      expect(response.status).toBe(401);
      expect(response.body).toHaveProperty('message');
    });
  });

  describe('authorize Middleware', () => {
    it('should allow admin access to admin route', async () => {
      const response = await request(app)
        .get('/admin-only')
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('message', 'Acesso admin permitido');
    });

    it('should deny user access to admin route', async () => {
      const response = await request(app)
        .get('/admin-only')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(403);
      expect(response.body).toHaveProperty('message');
    });

    it('should handle missing user role', async () => {
      // Criar token sem role
      const tokenSemRole = 'invalid-token';

      const response = await request(app)
        .get('/admin-only')
        .set('Authorization', `Bearer ${tokenSemRole}`);

      expect(response.status).toBe(401);
    });
  });

  describe('errorHandler Middleware', () => {
    it('should handle known errors', async () => {
      const response = await request(app)
        .get('/error');

      expect(response.status).toBe(500);
      expect(response.body).toHaveProperty('message');
      expect(response.body).toHaveProperty('error');
    });

    it('should handle validation errors', async () => {
      const testApp = express();
      testApp.use(express.json());
      testApp.post('/test', (req: Request, res: Response) => {
        throw {
          name: 'ValidationError',
          errors: [{ field: 'email', message: 'Email inválido' }]
        };
      });
      testApp.use(errorHandler);

      const response = await request(testApp)
        .post('/test')
        .send({});

      expect(response.status).toBe(400);
      expect(response.body).toHaveProperty('errors');
    });

    it('should handle database errors', async () => {
      const testApp = express();
      testApp.use(express.json());
      testApp.post('/test', (req: Request, res: Response) => {
        throw {
          name: 'PrismaClientKnownRequestError',
          code: 'P2002',
          message: 'Unique constraint failed'
        };
      });
      testApp.use(errorHandler);

      const response = await request(testApp)
        .post('/test')
        .send({});

      expect(response.status).toBe(400);
      expect(response.body).toHaveProperty('message');
    });
  });

  describe('notFound Middleware', () => {
    it('should handle 404 errors', async () => {
      const response = await request(app)
        .get('/non-existent-route');

      expect(response.status).toBe(404);
      expect(response.body).toHaveProperty('message');
    });

    it('should return JSON response', async () => {
      const response = await request(app)
        .get('/non-existent-route')
        .set('Accept', 'application/json');

      expect(response.type).toBe('application/json');
    });
  });

  describe('Middleware Integration', () => {
    it('should handle middleware chain correctly', async () => {
      const testApp = express();
      const middlewareOrder: string[] = [];

      // Middleware de teste
      const testMiddleware = (name: string) => (req: Request, res: Response, next: NextFunction) => {
        middlewareOrder.push(name);
        next();
      };

      testApp.use(testMiddleware('first'));
      testApp.use(authenticate);
      testApp.use(testMiddleware('second'));
      testApp.get('/test', (req: Request, res: Response) => {
        middlewareOrder.push('route');
        res.json({ order: middlewareOrder });
      });
      testApp.use(errorHandler);

      const response = await request(testApp)
        .get('/test')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);
      expect(response.body.order).toEqual(['first', 'second', 'route']);
    });

    it('should stop middleware chain on error', async () => {
      const testApp = express();
      const middlewareOrder: string[] = [];

      testApp.use((req: Request, res: Response, next: NextFunction) => {
        middlewareOrder.push('first');
        next(new Error('Erro de teste'));
      });

      testApp.use((req: Request, res: Response, next: NextFunction) => {
        middlewareOrder.push('second');
        next();
      });

      testApp.use(errorHandler);

      const response = await request(testApp)
        .get('/test');

      expect(response.status).toBe(500);
      expect(middlewareOrder).toEqual(['first']);
    });
  });
});