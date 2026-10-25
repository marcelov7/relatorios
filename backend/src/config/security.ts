import helmet from 'helmet';
import cors from 'cors';
import rateLimit from 'express-rate-limit';
import { Express } from 'express';
import { CORS, RATE_LIMIT } from './env';

// Configuração do rate limiter
const limiter = rateLimit({
  windowMs: RATE_LIMIT.WINDOW_MS,
  max: RATE_LIMIT.MAX_REQUESTS,
  message: 'Muitas requisições deste IP, por favor tente novamente mais tarde.'
});

// Configuração do CORS
const corsOptions = {
  origin: CORS.ORIGIN,
  credentials: CORS.CREDENTIALS,
  methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
  allowedHeaders: ['Content-Type', 'Authorization']
};

// Configuração do Helmet
const helmetOptions = {
  contentSecurityPolicy: {
    directives: {
      defaultSrc: ["'self'"],
      styleSrc: ["'self'", "'unsafe-inline'"],
      scriptSrc: ["'self'", "'unsafe-inline'", "'unsafe-eval'"],
      imgSrc: ["'self'", 'data:', 'blob:'],
      connectSrc: ["'self'"],
      fontSrc: ["'self'"],
      objectSrc: ["'none'"],
      mediaSrc: ["'self'"],
      frameSrc: ["'none'"]
    }
  },
  crossOriginEmbedderPolicy: false,
  crossOriginResourcePolicy: { policy: "cross-origin" }
};

// Função para configurar a segurança da aplicação
export function configureSecurity(app: Express): void {
  // Configurações básicas de segurança com Helmet
  app.use(helmet(helmetOptions));

  // Configuração do CORS
  app.use(cors(corsOptions));

  // Rate limiting
  app.use(limiter);

  // Desabilita o header X-Powered-By
  app.disable('x-powered-by');

  // Configurações adicionais de segurança
  app.use((req, res, next) => {
    // Previne clickjacking
    res.setHeader('X-Frame-Options', 'DENY');
    
    // Habilita a proteção XSS do navegador
    res.setHeader('X-XSS-Protection', '1; mode=block');
    
    // Previne MIME type sniffing
    res.setHeader('X-Content-Type-Options', 'nosniff');
    
    // Força HTTPS
    res.setHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    
    next();
  });
}