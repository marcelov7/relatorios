import { Router } from 'express';
import { createUser, updateUser, deleteUser, getUser, listUsers } from '../controllers/userController';
import { authorize } from '../middleware/authorize';

const router = Router();

// Rotas que requerem permissão de administrador
router.post('/', authorize(['ADMIN']), createUser);
router.put('/:id', authorize(['ADMIN']), updateUser);
router.delete('/:id', authorize(['ADMIN']), deleteUser);

// Rotas que requerem autenticação
router.get('/:id', authorize(['ADMIN', 'MANAGER']), getUser);
router.get('/', authorize(['ADMIN', 'MANAGER']), listUsers);

export { router as userRoutes };