import { Router } from 'express';
import { DashboardController } from '../controllers/dashboardController';
import { authenticate, authorize } from '../middleware/auth';

const router = Router();

/**
 * @route   GET /api/dashboard/stats
 * @desc    Obter estatísticas gerais do dashboard
 * @access  Private
 */
router.get('/stats', authenticate, DashboardController.getDashboardStats);

/**
 * @route   GET /api/dashboard/activities
 * @desc    Obter atividades recentes
 * @access  Private (Admin/Manager)
 */
router.get('/activities', authenticate, authorize('ADMIN', 'MANAGER'), DashboardController.getRecentActivities);

/**
 * @route   GET /api/dashboard/relatorios-periodo
 * @desc    Obter relatórios por período
 * @access  Private
 */
router.get('/relatorios-periodo', authenticate, DashboardController.getRelatoriosByPeriod);

export default router;