// Classe base para erros da aplicação
export class AppError extends Error {
  public readonly statusCode: number;

  constructor(message: string, statusCode = 400) {
    super(message);
    this.statusCode = statusCode;
    this.name = this.constructor.name;
    Error.captureStackTrace(this, this.constructor);
  }
}

// Erro de autenticação
export class AuthenticationError extends AppError {
  constructor(message = 'Não autorizado') {
    super(message, 401);
  }
}

// Erro de autorização
export class AuthorizationError extends AppError {
  constructor(message = 'Acesso negado') {
    super(message, 403);
  }
}

// Erro de não encontrado
export class NotFoundError extends AppError {
  constructor(message = 'Recurso não encontrado') {
    super(message, 404);
  }
}

// Erro de validação
export class ValidationError extends AppError {
  constructor(message = 'Dados inválidos') {
    super(message, 400);
  }
}

// Erro de conflito
export class ConflictError extends AppError {
  constructor(message = 'Conflito de recursos') {
    super(message, 409);
  }
}

// Erro interno do servidor
export class InternalServerError extends AppError {
  constructor(message = 'Erro interno do servidor') {
    super(message, 500);
  }
}