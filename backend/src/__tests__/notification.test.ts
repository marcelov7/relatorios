import { NotificationService } from '../services/notification';
import { PrismaClient } from '@prisma/client';
import { generateToken } from '../utils/auth';
import { setupTestEnvironment, teardownTestEnvironment } from '../utils/test';
import supertest from 'supertest';
import app from '../app';

const prisma = new PrismaClient();
const request = supertest(app);

describe('Notification System', () => {
  let adminToken: string;
  let userToken: string;
  let notificationService: NotificationService;

  beforeAll(async () => {
    await setupTestEnvironment();
    adminToken = generateToken({ id: 1, email: 'admin@test.com', role: 'ADMIN' });
    userToken = generateToken({ id: 2, email: 'user@test.com', role: 'USER' });
    notificationService = new NotificationService();
  });

  afterAll(async () => {
    await teardownTestEnvironment();
    await prisma.$disconnect();
  });

  describe('Notification Creation', () => {
    it('should create in-app notification', async () => {
      const notification = await notificationService.create({
        userId: 2,
        type: 'IN_APP',
        title: 'Test Notification',
        message: 'This is a test notification',
        priority: 'NORMAL'
      });

      expect(notification).toHaveProperty('id');
      expect(notification.type).toBe('IN_APP');
      expect(notification.status).toBe('UNREAD');
    });

    it('should create push notification', async () => {
      const notification = await notificationService.create({
        userId: 2,
        type: 'PUSH',
        title: 'Test Push',
        message: 'This is a test push notification',
        priority: 'HIGH'
      });

      expect(notification).toHaveProperty('id');
      expect(notification.type).toBe('PUSH');
      expect(notification.priority).toBe('HIGH');
    });

    it('should create email notification', async () => {
      const notification = await notificationService.create({
        userId: 2,
        type: 'EMAIL',
        title: 'Test Email',
        message: 'This is a test email notification',
        priority: 'LOW',
        metadata: {
          emailTemplate: 'BASIC',
          recipientEmail: 'user@test.com'
        }
      });

      expect(notification).toHaveProperty('id');
      expect(notification.type).toBe('EMAIL');
      expect(notification.metadata).toHaveProperty('emailTemplate');
    });
  });

  describe('Notification API Endpoints', () => {
    it('should require authentication for notifications', async () => {
      const response = await request.get('/api/notifications');
      expect(response.status).toBe(401);
    });

    it('should list user notifications', async () => {
      const response = await request
        .get('/api/notifications')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);
      expect(Array.isArray(response.body.notifications)).toBe(true);
    });

    it('should mark notification as read', async () => {
      // Criar uma notificação primeiro
      const notification = await notificationService.create({
        userId: 2,
        type: 'IN_APP',
        title: 'Test Read Status',
        message: 'This notification should be marked as read'
      });

      const response = await request
        .put(`/api/notifications/${notification.id}/read`)
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);
      expect(response.body.status).toBe('READ');
    });

    it('should delete notification', async () => {
      const notification = await notificationService.create({
        userId: 2,
        type: 'IN_APP',
        title: 'Test Delete',
        message: 'This notification should be deleted'
      });

      const response = await request
        .delete(`/api/notifications/${notification.id}`)
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);

      // Verificar se foi realmente deletado
      const checkResponse = await request
        .get(`/api/notifications/${notification.id}`)
        .set('Authorization', `Bearer ${userToken}`);

      expect(checkResponse.status).toBe(404);
    });
  });

  describe('Notification Preferences', () => {
    it('should update notification preferences', async () => {
      const preferences = {
        emailEnabled: true,
        pushEnabled: false,
        inAppEnabled: true,
        emailFrequency: 'DAILY',
        priorities: ['HIGH', 'NORMAL']
      };

      const response = await request
        .put('/api/notifications/preferences')
        .set('Authorization', `Bearer ${userToken}`)
        .send(preferences);

      expect(response.status).toBe(200);
      expect(response.body).toMatchObject(preferences);
    });

    it('should get notification preferences', async () => {
      const response = await request
        .get('/api/notifications/preferences')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('emailEnabled');
      expect(response.body).toHaveProperty('pushEnabled');
    });
  });

  describe('Notification Filters', () => {
    it('should filter notifications by type', async () => {
      const response = await request
        .get('/api/notifications?type=IN_APP')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);
      expect(response.body.notifications.every((n: any) => n.type === 'IN_APP')).toBe(true);
    });

    it('should filter notifications by status', async () => {
      const response = await request
        .get('/api/notifications?status=UNREAD')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);
      expect(response.body.notifications.every((n: any) => n.status === 'UNREAD')).toBe(true);
    });

    it('should filter notifications by priority', async () => {
      const response = await request
        .get('/api/notifications?priority=HIGH')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(200);
      expect(response.body.notifications.every((n: any) => n.priority === 'HIGH')).toBe(true);
    });
  });

  describe('Batch Operations', () => {
    it('should mark multiple notifications as read', async () => {
      // Criar várias notificações
      const notifications = await Promise.all([
        notificationService.create({
          userId: 2,
          type: 'IN_APP',
          title: 'Batch Test 1',
          message: 'Test message 1'
        }),
        notificationService.create({
          userId: 2,
          type: 'IN_APP',
          title: 'Batch Test 2',
          message: 'Test message 2'
        })
      ]);

      const notificationIds = notifications.map(n => n.id);

      const response = await request
        .put('/api/notifications/batch/read')
        .set('Authorization', `Bearer ${userToken}`)
        .send({ notificationIds });

      expect(response.status).toBe(200);
      expect(response.body.updatedCount).toBe(notificationIds.length);
    });

    it('should delete multiple notifications', async () => {
      const notifications = await Promise.all([
        notificationService.create({
          userId: 2,
          type: 'IN_APP',
          title: 'Delete Test 1',
          message: 'Test message 1'
        }),
        notificationService.create({
          userId: 2,
          type: 'IN_APP',
          title: 'Delete Test 2',
          message: 'Test message 2'
        })
      ]);

      const notificationIds = notifications.map(n => n.id);

      const response = await request
        .delete('/api/notifications/batch')
        .set('Authorization', `Bearer ${userToken}`)
        .send({ notificationIds });

      expect(response.status).toBe(200);
      expect(response.body.deletedCount).toBe(notificationIds.length);
    });
  });

  describe('Error Handling', () => {
    it('should handle invalid notification type', async () => {
      const response = await request
        .post('/api/notifications')
        .set('Authorization', `Bearer ${userToken}`)
        .send({
          type: 'INVALID_TYPE',
          title: 'Test',
          message: 'Test'
        });

      expect(response.status).toBe(400);
    });

    it('should handle missing required fields', async () => {
      const response = await request
        .post('/api/notifications')
        .set('Authorization', `Bearer ${userToken}`)
        .send({
          type: 'IN_APP'
          // Faltando title e message
        });

      expect(response.status).toBe(400);
    });

    it('should handle notification not found', async () => {
      const response = await request
        .get('/api/notifications/999999')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(404);
    });

    it('should handle unauthorized access', async () => {
      const notification = await notificationService.create({
        userId: 1, // Pertence ao admin
        type: 'IN_APP',
        title: 'Admin Notification',
        message: 'This belongs to admin'
      });

      const response = await request
        .delete(`/api/notifications/${notification.id}`)
        .set('Authorization', `Bearer ${userToken}`); // Tentando acessar com usuário comum

      expect(response.status).toBe(403);
    });
  });
});