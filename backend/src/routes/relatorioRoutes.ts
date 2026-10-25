import { Router } from 'express';
import { createRelatorio, updateRelatorio, deleteRelatorio, getRelatorio, listRelatorios, uploadAnexos } from '../controllers/relatorioController';
import { authorize } from '../middleware/authorize';
import { upload } from '../config/upload';

const router = Router();

// Rotas que requerem permissão de usuário ou superior
router.post('/', authorize(['ADMIN', 'MANAGER', 'USER']), createRelatorio);
router.put('/:id', authorize(['ADMIN', 'MANAGER', 'USER']), updateRelatorio);

// Rota de upload de anexos
router.post('/:id/anexos', 
  authorize(['ADMIN', 'MANAGER', 'USER']),
  upload.array('files', 5), // Máximo de 5 arquivos por upload
  uploadAnexos
);

// Rotas que requerem permissão de administrador
router.delete('/:id', authorize(['ADMIN']), deleteRelatorio);

// Rotas que requerem autenticação
router.get('/:id', authorize(['ADMIN', 'MANAGER', 'USER']), getRelatorio);
router.get('/', authorize(['ADMIN', 'MANAGER', 'USER']), listRelatorios);

export { router as relatorioRoutes };