import { Router } from 'express';
import { authRoutes } from './authRoutes';
import { userRoutes } from './userRoutes';
import { setorRoutes } from './setorRoutes';
import { equipamentoRoutes } from './equipamentoRoutes';
import { relatorioRoutes } from './relatorioRoutes';
import { authenticate } from '../middleware/authenticate';

const router = Router();

// Rotas públicas de autenticação
router.use('/auth', authRoutes);

// Rotas protegidas por autenticação
router.use('/users', authenticate, userRoutes);
router.use('/setores', authenticate, setorRoutes);
router.use('/equipamentos', authenticate, equipamentoRoutes);
router.use('/relatorios', authenticate, relatorioRoutes);

export { router as routes };