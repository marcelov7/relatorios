import dotenv from 'dotenv';
import path from 'path';

// Carrega as variáveis de ambiente do arquivo .env
dotenv.config();

// Configurações do servidor
export const SERVER = {
  PORT: process.env.PORT || 3000,
  NODE_ENV: process.env.NODE_ENV || 'development',
  API_URL: process.env.API_URL || 'http://localhost:3000',
  FRONTEND_URL: process.env.FRONTEND_URL || 'http://localhost:3001'
};

// Configurações do banco de dados
export const DATABASE = {
  URL: process.env.DATABASE_URL || 'postgresql://user:password@localhost:5432/database'
};

// Configurações de autenticação
export const AUTH = {
  JWT_SECRET: process.env.JWT_SECRET || 'default_secret_key_change_in_production',
  JWT_EXPIRES_IN: process.env.JWT_EXPIRES_IN || '1d'
};

// Configurações de upload
export const UPLOAD = {
  PATH: process.env.UPLOAD_PATH || path.resolve(__dirname, '..', '..', 'uploads'),
  MAX_SIZE: Number(process.env.MAX_FILE_SIZE) || 5 * 1024 * 1024 // 5MB
};

// Configurações de e-mail
export const EMAIL = {
  SMTP_HOST: process.env.SMTP_HOST || 'smtp.gmail.com',
  SMTP_PORT: Number(process.env.SMTP_PORT) || 587,
  SMTP_USER: process.env.SMTP_USER,
  SMTP_PASS: process.env.SMTP_PASS
};

// Configurações de CORS
export const CORS = {
  ORIGIN: process.env.CORS_ORIGIN || '*',
  CREDENTIALS: process.env.CORS_CREDENTIALS === 'true'
};

// Configurações de rate limiting
export const RATE_LIMIT = {
  WINDOW_MS: Number(process.env.RATE_LIMIT_WINDOW_MS) || 15 * 60 * 1000, // 15 minutos
  MAX_REQUESTS: Number(process.env.RATE_LIMIT_MAX_REQUESTS) || 100
};