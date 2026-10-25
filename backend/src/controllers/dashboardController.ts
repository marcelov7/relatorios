import { Request, Response, NextFunction } from 'express';
import prisma from '../config/database';
import { successResponse } from '../utils/helpers';
import { DashboardStats } from '../types';

export class DashboardController {
  // Obter estatísticas gerais do dashboard
  static async getDashboardStats(req: Request, res: Response, next: NextFunction) {
    try {
      const isAdmin = req.user?.role === 'ADMIN' || req.user?.role === 'MANAGER';
      const userId = isAdmin ? undefined : req.user?.id;

      // Filtro base para relatórios (se não for admin, mostrar apenas do usuário)
      const relatorioWhere = userId ? { userId } : {};

      // Estatísticas básicas
      const [
        totalRelatorios,
        relatoriosPendentes,
        relatoriosEmAndamento,
        relatoriosConcluidos,
        totalEquipamentos,
        equipamentosAtivos,
        equipamentosManutencao,
        totalSetores,
        setoresAtivos,
        totalUsuarios
      ] = await Promise.all([
        prisma.relatorio.count({ where: relatorioWhere }),
        prisma.relatorio.count({ where: { ...relatorioWhere, status: 'PENDENTE' } }),
        prisma.relatorio.count({ where: { ...relatorioWhere, status: 'EM_ANDAMENTO' } }),
        prisma.relatorio.count({ where: { ...relatorioWhere, status: 'CONCLUIDO' } }),
        isAdmin ? prisma.equipamento.count() : 0,
        isAdmin ? prisma.equipamento.count({ where: { status: 'ATIVO' } }) : 0,
        isAdmin ? prisma.equipamento.count({ where: { status: 'MANUTENCAO' } }) : 0,
        isAdmin ? prisma.setor.count() : 0,
        isAdmin ? prisma.setor.count({ where: { active: true } }) : 0,
        isAdmin ? prisma.user.count({ where: { active: true } }) : 0
      ]);

      // Relatórios por prioridade
      const [prioridadeBaixa, prioridadeMedia, prioridadeAlta, prioridadeCritica] = await Promise.all([
        prisma.relatorio.count({ where: { ...relatorioWhere, prioridade: 'BAIXA' } }),
        prisma.relatorio.count({ where: { ...relatorioWhere, prioridade: 'MEDIA' } }),
        prisma.relatorio.count({ where: { ...relatorioWhere, prioridade: 'ALTA' } }),
        prisma.relatorio.count({ where: { ...relatorioWhere, prioridade: 'CRITICA' } })
      ]);

      // Relatórios recentes (últimos 7 dias)
      const sevenDaysAgo = new Date();
      sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7);

      const relatoriosRecentes = await prisma.relatorio.count({
        where: {
          ...relatorioWhere,
          createdAt: {
            gte: sevenDaysAgo
          }
        }
      });

      // Relatórios por mês (últimos 6 meses)
      const sixMonthsAgo = new Date();
      sixMonthsAgo.setMonth(sixMonthsAgo.getMonth() - 6);

      const relatoriosPorMes = await prisma.relatorio.groupBy({
        by: ['createdAt'],
        where: {
          ...relatorioWhere,
          createdAt: {
            gte: sixMonthsAgo
          }
        },
        _count: {
          id: true
        }
      });

      // Processar dados por mês
      const monthlyData = Array.from({ length: 6 }, (_, i) => {
        const date = new Date();
        date.setMonth(date.getMonth() - i);
        const monthYear = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
        
        const count = relatoriosPorMes.filter(item => {
          const itemDate = new Date(item.createdAt);
          const itemMonthYear = `${itemDate.getFullYear()}-${String(itemDate.getMonth() + 1).padStart(2, '0')}`;
          return itemMonthYear === monthYear;
        }).reduce((sum, item) => sum + item._count.id, 0);

        return {
          month: monthYear,
          count
        };
      }).reverse();

      // Top 5 equipamentos com mais relatórios
      const topEquipamentos = await prisma.equipamento.findMany({
        select: {
          id: true,
          nome: true,
          equipmentTag: true,
          _count: {
            select: {
              relatorios: true
            }
          }
        },
        orderBy: {
          relatorios: {
            _count: 'desc'
          }
        },
        take: 5
      });

      // Top 5 setores com mais relatórios
      const topSetores = await prisma.setor.findMany({
        select: {
          id: true,
          nome: true,
          _count: {
            select: {
              relatorios: true
            }
          }
        },
        orderBy: {
          relatorios: {
            _count: 'desc'
          }
        },
        take: 5
      });

      // Relatórios urgentes (prioridade ALTA ou CRITICA e status PENDENTE ou EM_ANDAMENTO)
      const relatoriosUrgentes = await prisma.relatorio.findMany({
        where: {
          ...relatorioWhere,
          prioridade: {
            in: ['ALTA', 'CRITICA']
          },
          status: {
            in: ['PENDENTE', 'EM_ANDAMENTO']
          }
        },
        include: {
          equipamento: {
            select: {
              nome: true,
              equipmentTag: true
            }
          },
          setor: {
            select: {
              nome: true
            }
          }
        },
        orderBy: [
          { prioridade: 'desc' },
          { createdAt: 'desc' }
        ],
        take: 10
      });

      const stats: DashboardStats = {
        totalRelatorios,
        relatoriosPendentes,
        relatoriosEmAndamento,
        relatoriosConcluidos,
        totalEquipamentos,
        equipamentosAtivos,
        equipamentosManutencao,
        totalSetores,
        totalUsuarios,
        relatoriosPorMes: monthlyData.map(item => ({ mes: item.month, total: item.count })),
        topEquipamentos: topEquipamentos.map(eq => ({
          nome: eq.nome,
          tag: eq.equipmentTag,
          count: eq._count.relatorios
        })),
        topSetores: topSetores.map(setor => ({
          nome: setor.nome,
          count: setor._count.relatorios
        })),
        relatoriosUrgentes: relatoriosUrgentes.map(rel => ({
          id: rel.id,
          titulo: rel.titulo,
          prioridade: rel.prioridade,
          status: rel.status,
          equipamento: rel.equipamento.nome,
          equipmentTag: rel.equipamento.equipmentTag,
          setor: rel.setor.nome,
          createdAt: rel.createdAt
        }))
      };

      res.json(successResponse(stats, 'Estatísticas do dashboard obtidas com sucesso'));

    } catch (error) {
      next(error);
    }
  }

  // Obter atividades recentes
  static async getRecentActivities(req: Request, res: Response, next: NextFunction) {
    try {
      const isAdmin = req.user?.role === 'ADMIN' || req.user?.role === 'MANAGER';
      const userId = isAdmin ? undefined : req.user?.id;

      const relatorioWhere = userId ? { userId } : {};

      // Últimos 20 relatórios criados ou atualizados
      const recentRelatorios = await prisma.relatorio.findMany({
        where: relatorioWhere,
        include: {
          user: {
            select: {
              name: true
            }
          },
          equipamento: {
            select: {
              nome: true,
              equipmentTag: true
            }
          },
          setor: {
            select: {
              nome: true
            }
          }
        },
        orderBy: { updatedAt: 'desc' },
        take: 20
      });

      const activities = recentRelatorios.map(rel => ({
        id: rel.id,
        type: 'relatorio',
        action: rel.createdAt.getTime() === rel.updatedAt.getTime() ? 'created' : 'updated',
        title: rel.titulo,
        user: rel.user.name,
        equipamento: rel.equipamento.nome,
        equipmentTag: rel.equipamento.equipmentTag,
        setor: rel.setor.nome,
        status: rel.status,
        prioridade: rel.prioridade,
        timestamp: rel.updatedAt
      }));

      res.json(successResponse(activities, 'Atividades recentes obtidas com sucesso'));

    } catch (error) {
      next(error);
    }
  }

  // Obter relatórios por período
  static async getRelatoriosByPeriod(req: Request, res: Response, next: NextFunction) {
    try {
      const { startDate, endDate } = req.query;
      
      if (!startDate || !endDate) {
        return next(new Error('Datas de início e fim são obrigatórias'));
      }

      const isAdmin = req.user?.role === 'ADMIN' || req.user?.role === 'MANAGER';
      const userId = isAdmin ? undefined : req.user?.id;

      const where: any = {
        createdAt: {
          gte: new Date(startDate as string),
          lte: new Date(endDate as string)
        }
      };

      if (userId) {
        where.userId = userId;
      }

      const relatorios = await prisma.relatorio.findMany({
        where,
        include: {
          user: {
            select: {
              name: true
            }
          },
          equipamento: {
            select: {
              nome: true,
              equipmentTag: true
            }
          },
          setor: {
            select: {
              nome: true
            }
          }
        },
        orderBy: { createdAt: 'desc' }
      });

      // Agrupar por status
      const byStatus = relatorios.reduce((acc, rel) => {
        acc[rel.status] = (acc[rel.status] || 0) + 1;
        return acc;
      }, {} as Record<string, number>);

      // Agrupar por prioridade
      const byPrioridade = relatorios.reduce((acc, rel) => {
        acc[rel.prioridade] = (acc[rel.prioridade] || 0) + 1;
        return acc;
      }, {} as Record<string, number>);

      // Agrupar por dia
      const byDay = relatorios.reduce((acc, rel) => {
        const day = rel.createdAt.toISOString().split('T')[0];
        acc[day] = (acc[day] || 0) + 1;
        return acc;
      }, {} as Record<string, number>);

      const result = {
        total: relatorios.length,
        relatorios,
        summary: {
          byStatus,
          byPrioridade,
          byDay
        }
      };

      res.json(successResponse(result, 'Relatórios por período obtidos com sucesso'));

    } catch (error) {
      next(error);
    }
  }
}