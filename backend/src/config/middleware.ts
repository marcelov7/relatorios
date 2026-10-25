import express from 'express';
import cors from 'cors';
import helmet from 'helmet';
import rateLimit from 'express-rate-limit';
import { RATE_LIMIT } from './constants';
import { errorHandler } from '../middleware/errorHandler';
import { upload } from './upload';

// Configuração do rate limiter
const limiter = rateLimit({
  windowMs: RATE_LIMIT.WINDOW_MS,
  max: RATE_LIMIT.MAX_REQUESTS
});

// Função para configurar os middlewares globais
export function configureMiddleware(app: express.Application): void {
  // Middlewares básicos
  app.use(express.json());
  app.use(express.urlencoded({ extended: true }));
  
  // Segurança
  app.use(helmet());
  app.use(cors({
    origin: process.env.FRONTEND_URL || 'http://localhost:3000',
    credentials: true
  }));
  
  // Rate limiting
  app.use(limiter);
  
  // Configuração de uploads
  app.use('/uploads', express.static(upload.directory));
  
  // Tratamento de erros
  app.use(errorHandler);
}