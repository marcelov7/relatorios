import { Router } from 'express';
import { authRoutes } from '../routes/authRoutes';
import { userRoutes } from '../routes/userRoutes';
import { setorRoutes } from '../routes/setorRoutes';
import { equipamentoRoutes } from '../routes/equipamentoRoutes';
import { relatorioRoutes } from '../routes/relatorioRoutes';
import { authenticate } from '../middleware/authenticate';

// Criação do roteador principal
const router = Router();

// Configuração das rotas públicas
router.use('/auth', authRoutes);

// Configuração das rotas protegidas
router.use('/users', authenticate, userRoutes);
router.use('/setores', authenticate, setorRoutes);
router.use('/equipamentos', authenticate, equipamentoRoutes);
router.use('/relatorios', authenticate, relatorioRoutes);

// Exportação do roteador configurado
export { router as routes };