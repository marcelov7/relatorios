import { Request } from 'express';
import { User } from '@prisma/client';

// Interface para requisição autenticada
export interface AuthenticatedRequest extends Request {
  user?: User;
}

// Interface para opções de paginação
export interface PaginationOptions {
  page?: number;
  limit?: number;
  search?: string;
  sortBy?: string;
  sortOrder?: 'asc' | 'desc';
}

// Interface para resposta paginada
export interface PaginatedResponse<T> {
  items: T[];
  total: number;
  page: number;
  limit: number;
  totalPages: number;
  hasNext: boolean;
  hasPrev: boolean;
}

// Interface para resposta de erro
export interface ErrorResponse {
  statusCode: number;
  message: string;
  error?: string;
  stack?: string;
}

// Interface para configuração de upload
export interface UploadConfig {
  directory: string;
  maxSize: number;
  allowedMimes: string[];
  maxFiles: number;
}

// Interface para configuração de e-mail
export interface EmailConfig {
  host: string;
  port: number;
  secure: boolean;
  auth: {
    user: string;
    pass: string;
  };
}

// Interface para configuração de cache
export interface CacheConfig {
  ttl: number;
  maxItems: number;
}

// Interface para configuração de rate limit
export interface RateLimitConfig {
  windowMs: number;
  max: number;
}

// Interface para configuração de logs
export interface LogConfig {
  level: string;
  maxSize: number;
  maxFiles: number;
  filename: {
    error: string;
    combined: string;
  };
}