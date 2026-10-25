import { Router } from 'express';
import { login, register, me } from '../controllers/authController';
import { authenticate } from '../middleware/authenticate';

const router = Router();

// Rotas públicas
router.post('/login', login);
router.post('/register', register);

// Rota protegida
router.get('/me', authenticate, me);

export { router as authRoutes };