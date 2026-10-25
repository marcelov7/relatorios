import { MetricsService } from '../services/metrics';
import { PrismaClient } from '@prisma/client';
import { generateToken } from '../utils/auth';
import { setupTestEnvironment, teardownTestEnvironment } from '../utils/test';
import supertest from 'supertest';
import app from '../app';

const prisma = new PrismaClient();
const request = supertest(app);

describe('Metrics System', () => {
  let adminToken: string;
  let userToken: string;
  let metricsService: MetricsService;

  beforeAll(async () => {
    await setupTestEnvironment();
    adminToken = generateToken({ id: 1, email: 'admin@test.com', role: 'ADMIN' });
    userToken = generateToken({ id: 2, email: 'user@test.com', role: 'USER' });
    metricsService = new MetricsService();
  });

  afterAll(async () => {
    await teardownTestEnvironment();
    await prisma.$disconnect();
  });

  describe('Performance Metrics', () => {
    it('should track API response times', async () => {
      const metric = await metricsService.trackApiResponse({
        endpoint: '/api/test',
        method: 'GET',
        responseTime: 150,
        statusCode: 200
      });

      expect(metric).toHaveProperty('id');
      expect(metric.type).toBe('API_RESPONSE');
      expect(metric.value).toBe(150);
    });

    it('should track database query times', async () => {
      const metric = await metricsService.trackDatabaseQuery({
        query: 'SELECT * FROM users',
        executionTime: 50,
        rowCount: 100
      });

      expect(metric).toHaveProperty('id');
      expect(metric.type).toBe('DB_QUERY');
      expect(metric.value).toBe(50);
    });

    it('should track memory usage', async () => {
      const metric = await metricsService.trackMemoryUsage({
        heapUsed: 50000000,
        heapTotal: 100000000
      });

      expect(metric).toHaveProperty('id');
      expect(metric.type).toBe('MEMORY_USAGE');
      expect(metric.metadata).toHaveProperty('heapUsed');
    });
  });

  describe('Business Metrics', () => {
    it('should track user activity', async () => {
      const metric = await metricsService.trackUserActivity({
        userId: 2,
        action: 'LOGIN',
        details: { browser: 'Chrome', platform: 'Windows' }
      });

      expect(metric).toHaveProperty('id');
      expect(metric.type).toBe('USER_ACTIVITY');
      expect(metric.metadata.action).toBe('LOGIN');
    });

    it('should track report generation', async () => {
      const metric = await metricsService.trackReportGeneration({
        reportId: 1,
        generationTime: 2000,
        pageCount: 5
      });

      expect(metric).toHaveProperty('id');
      expect(metric.type).toBe('REPORT_GENERATION');
      expect(metric.value).toBe(2000);
    });
  });

  describe('Metrics API Endpoints', () => {
    it('should require admin access for metrics', async () => {
      const response = await request
        .get('/api/metrics')
        .set('Authorization', `Bearer ${userToken}`);

      expect(response.status).toBe(403);
    });

    it('should get performance metrics', async () => {
      const response = await request
        .get('/api/metrics/performance')
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('apiResponseTimes');
      expect(response.body).toHaveProperty('databaseQueryTimes');
    });

    it('should get business metrics', async () => {
      const response = await request
        .get('/api/metrics/business')
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('userActivity');
      expect(response.body).toHaveProperty('reportGeneration');
    });
  });

  describe('Metrics Aggregation', () => {
    it('should aggregate metrics by time period', async () => {
      const response = await request
        .get('/api/metrics/aggregate')
        .query({ period: 'daily', type: 'API_RESPONSE' })
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('data');
      expect(Array.isArray(response.body.data)).toBe(true);
    });

    it('should calculate statistics', async () => {
      const response = await request
        .get('/api/metrics/stats')
        .query({ type: 'API_RESPONSE' })
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(200);
      expect(response.body).toHaveProperty('average');
      expect(response.body).toHaveProperty('median');
      expect(response.body).toHaveProperty('p95');
    });
  });

  describe('Error Handling', () => {
    it('should handle invalid metric type', async () => {
      const response = await request
        .get('/api/metrics/aggregate')
        .query({ type: 'INVALID_TYPE' })
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(400);
    });

    it('should handle invalid time period', async () => {
      const response = await request
        .get('/api/metrics/aggregate')
        .query({ period: 'invalid' })
        .set('Authorization', `Bearer ${adminToken}`);

      expect(response.status).toBe(400);
    });
  });