import { Request, Response, NextFunction } from 'express';

export interface AppError extends Error {
  statusCode?: number;
  isOperational?: boolean;
}

export const errorHandler = (
  err: AppError,
  req: Request,
  res: Response,
  next: NextFunction
) => {
  let error = { ...err };
  error.message = err.message;

  // Log do erro
  console.error('❌ Erro:', err);

  // Erro de validação do Prisma
  if (err.name === 'PrismaClientValidationError') {
    const message = 'Dados inválidos fornecidos';
    error = { ...error, statusCode: 400, message };
  }

  // Erro de registro não encontrado do Prisma
  if (err.name === 'NotFoundError') {
    const message = 'Recurso não encontrado';
    error = { ...error, statusCode: 404, message };
  }

  // Erro de violação de constraint única do Prisma
  if (err.name === 'PrismaClientKnownRequestError') {
    if ((err as any).code === 'P2002') {
      const message = 'Recurso já existe';
      error = { ...error, statusCode: 409, message };
    }
    if ((err as any).code === 'P2025') {
      const message = 'Recurso não encontrado';
      error = { ...error, statusCode: 404, message };
    }
  }

  // Erro de JWT
  if (err.name === 'JsonWebTokenError') {
    const message = 'Token inválido';
    error = { ...error, statusCode: 401, message };
  }

  if (err.name === 'TokenExpiredError') {
    const message = 'Token expirado';
    error = { ...error, statusCode: 401, message };
  }

  // Erro de validação do Joi
  if (err.name === 'ValidationError') {
    const message = (err as any).details?.map((detail: any) => detail.message).join(', ') || 'Dados inválidos';
    error = { ...error, statusCode: 400, message };
  }

  res.status(error.statusCode || 500).json({
    success: false,
    error: error.message || 'Erro interno do servidor',
    ...(process.env.NODE_ENV === 'development' && { stack: err.stack })
  });
};

export const createError = (message: string, statusCode: number = 500): AppError => {
  const error: AppError = new Error(message);
  error.statusCode = statusCode;
  error.isOperational = true;
  return error;
};