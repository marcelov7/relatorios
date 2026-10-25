import jwt from 'jsonwebtoken';
import bcrypt from 'bcryptjs';
import { User } from '@prisma/client';

// Interface para o payload do token JWT
export interface JwtPayload {
  id: string;
  username: string;
  email: string;
  role: string;
}

// Configurações do JWT
const jwtConfig = {
  secret: process.env.JWT_SECRET || 'default_secret_key_change_in_production',
  expiresIn: process.env.JWT_EXPIRES_IN || '1d'
};

// Função para gerar token JWT
export function generateToken(user: User): string {
  const payload: JwtPayload = {
    id: user.id,
    username: user.username,
    email: user.email,
    role: user.role
  };

  return jwt.sign(payload, jwtConfig.secret, {
    expiresIn: jwtConfig.expiresIn
  });
}

// Função para verificar token JWT
export function verifyToken(token: string): JwtPayload {
  return jwt.verify(token, jwtConfig.secret) as JwtPayload;
}

// Função para gerar hash de senha
export async function hashPassword(password: string): Promise<string> {
  const salt = await bcrypt.genSalt(10);
  return bcrypt.hash(password, salt);
}

// Função para comparar senha com hash
export async function comparePassword(password: string, hash: string): Promise<boolean> {
  return bcrypt.compare(password, hash);
}

export { jwtConfig };