import { Router } from 'express';
import { createSetor, updateSetor, deleteSetor, getSetor, listSetores } from '../controllers/setorController';
import { authorize } from '../middleware/authorize';

const router = Router();

// Rotas que requerem permissão de administrador ou gerente
router.post('/', authorize(['ADMIN', 'MANAGER']), createSetor);
router.put('/:id', authorize(['ADMIN', 'MANAGER']), updateSetor);
router.delete('/:id', authorize(['ADMIN']), deleteSetor);

// Rotas que requerem autenticação
router.get('/:id', authorize(['ADMIN', 'MANAGER', 'USER']), getSetor);
router.get('/', authorize(['ADMIN', 'MANAGER', 'USER']), listSetores);

export { router as setorRoutes };