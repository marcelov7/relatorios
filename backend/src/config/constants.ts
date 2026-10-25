// Configurações de paginação
export const PAGINATION = {
  DEFAULT_PAGE: 1,
  DEFAULT_LIMIT: 10,
  MAX_LIMIT: 100
};

// Configurações de upload
export const UPLOAD = {
  MAX_FILE_SIZE: 5 * 1024 * 1024, // 5MB
  ALLOWED_MIME_TYPES: [
    'image/jpeg',
    'image/jpg',
    'image/png',
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
  ],
  MAX_FILES: 5
};

// Configurações de autenticação
export const AUTH = {
  TOKEN_PREFIX: 'Bearer',
  SALT_ROUNDS: 10,
  PASSWORD_MIN_LENGTH: 6,
  USERNAME_MIN_LENGTH: 3,
  USERNAME_MAX_LENGTH: 50,
  NAME_MIN_LENGTH: 3,
  NAME_MAX_LENGTH: 100
};

// Configurações de cache
export const CACHE = {
  TTL: 60 * 60 * 1000, // 1 hora em milissegundos
  MAX_ITEMS: 100
};

// Configurações de rate limiting
export const RATE_LIMIT = {
  WINDOW_MS: 15 * 60 * 1000, // 15 minutos
  MAX_REQUESTS: 100
};

// Configurações de e-mail
export const EMAIL = {
  FROM: 'noreply@sistema.com',
  SUBJECT_PREFIX: '[Sistema de Relatórios] '
};

// Configurações de logs
export const LOGS = {
  MAX_SIZE: 5242880, // 5MB
  MAX_FILES: 5,
  FILENAME: {
    ERROR: 'error.log',
    COMBINED: 'combined.log'
  }
};