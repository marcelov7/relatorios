import { Router } from 'express';
import { createEquipamento, updateEquipamento, deleteEquipamento, getEquipamento, listEquipamentos } from '../controllers/equipamentoController';
import { authorize } from '../middleware/authorize';

const router = Router();

// Rotas que requerem permissão de administrador ou gerente
router.post('/', authorize(['ADMIN', 'MANAGER']), createEquipamento);
router.put('/:id', authorize(['ADMIN', 'MANAGER']), updateEquipamento);
router.delete('/:id', authorize(['ADMIN']), deleteEquipamento);

// Rotas que requerem autenticação
router.get('/:id', authorize(['ADMIN', 'MANAGER', 'USER']), getEquipamento);
router.get('/', authorize(['ADMIN', 'MANAGER', 'USER']), listEquipamentos);

export { router as equipamentoRoutes };