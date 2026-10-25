import { Request, Response, NextFunction } from 'express';
import prisma from '../config/database';
import { getPaginationParams, createPaginatedResponse, successResponse } from '../utils/helpers';
import { createRelatorioSchema, updateRelatorioSchema, paginationSchema } from '../utils/validation';
import { createError } from '../middleware/errorHandler';
import { CreateRelatorioDto, UpdateRelatorioDto } from '../types';

export class RelatorioController {
  // Listar relatórios com paginação e filtros
  static async getRelatorios(req: Request, res: Response, next: NextFunction) {
    try {
      // Validar parâmetros de paginação
      const { error, value } = paginationSchema.validate(req.query);
      if (error) {
        return next(createError(error.details[0].message, 400));
      }

      const params = getPaginationParams(value);
      const { page = 1, limit = 10, search, sortBy = 'createdAt', sortOrder = 'desc' } = params;
      const { setorId, equipamentoId, status, prioridade, userId } = req.query;

      // Construir filtros de busca
      const where: any = {};
      
      if (search) {
        where.OR = [
          { titulo: { contains: search, mode: 'insensitive' } },
          { descricao: { contains: search, mode: 'insensitive' } },
          { observacoes: { contains: search, mode: 'insensitive' } }
        ];
      }

      if (setorId) {
        where.setorId = setorId;
      }

      if (equipamentoId) {
        where.equipamentoId = equipamentoId;
      }

      if (status) {
        where.status = status;
      }

      if (prioridade) {
        where.prioridade = prioridade;
      }

      if (userId) {
        where.userId = userId;
      }

      // Se não for admin, mostrar apenas relatórios do usuário
      if (req.user?.role !== 'ADMIN' && req.user?.role !== 'MANAGER') {
        where.userId = req.user?.id;
      }

      // Contar total de registros
      const total = await prisma.relatorio.count({ where });

      // Buscar relatórios
      const relatorios = await prisma.relatorio.findMany({
        where,
        include: {
          user: {
            select: {
              id: true,
              name: true,
              email: true
            }
          },
          setor: {
            select: {
              id: true,
              nome: true
            }
          },
          equipamento: {
            select: {
              id: true,
              nome: true,
              equipmentTag: true
            }
          },
          imagens: {
            select: {
              id: true,
              filename: true,
              originalName: true,
              path: true
            }
          },
          _count: {
            select: {
              imagens: true
            }
          }
        },
        orderBy: { [sortBy as string]: sortOrder },
        skip: (page - 1) * limit,
        take: limit
      });

      const response = createPaginatedResponse(relatorios, total, params);
      res.json(successResponse(response, 'Relatórios obtidos com sucesso'));

    } catch (error) {
      next(error);
    }
  }

  // Obter relatório por ID
  static async getRelatorioById(req: Request, res: Response, next: NextFunction) {
    try {
      const { id } = req.params;

      const relatorio = await prisma.relatorio.findUnique({
        where: { id },
        include: {
          user: {
            select: {
              id: true,
              name: true,
              email: true
            }
          },
          setor: {
            select: {
              id: true,
              nome: true,
              descricao: true
            }
          },
          equipamento: {
            select: {
              id: true,
              nome: true,
              equipmentTag: true,
              descricao: true,
              status: true
            }
          },
          imagens: {
            select: {
              id: true,
              filename: true,
              originalName: true,
              path: true,
              size: true,
              createdAt: true
            },
            orderBy: { createdAt: 'asc' }
          }
        }
      });

      if (!relatorio) {
        return next(createError('Relatório não encontrado', 404));
      }

      // Verificar permissão de acesso
      if (req.user?.role !== 'ADMIN' && req.user?.role !== 'MANAGER' && relatorio.userId !== req.user?.id) {
        return next(createError('Acesso negado', 403));
      }

      res.json(successResponse(relatorio, 'Relatório obtido com sucesso'));

    } catch (error) {
      next(error);
    }
  }

  // Criar relatório
  static async createRelatorio(req: Request, res: Response, next: NextFunction) {
    try {
      // Validar dados de entrada
      const { error, value } = createRelatorioSchema.validate(req.body);
      if (error) {
        return next(createError(error.details[0].message, 400));
      }

      const { 
        titulo, 
        descricao, 
        prioridade, 
        dataInicio, 
        dataFim, 
        observacoes, 
        equipamentoId, 
        setorId 
      }: CreateRelatorioDto = value;

      // Verificar se o equipamento existe
      const equipamento = await prisma.equipamento.findUnique({
        where: { id: equipamentoId }
      });

      if (!equipamento) {
        return next(createError('Equipamento não encontrado', 404));
      }

      // Verificar se o setor existe
      const setor = await prisma.setor.findUnique({
        where: { id: setorId }
      });

      if (!setor) {
        return next(createError('Setor não encontrado', 404));
      }

      if (!setor.active) {
        return next(createError('Setor está inativo', 400));
      }

      // Criar relatório
      const relatorio = await prisma.relatorio.create({
        data: {
          titulo,
          descricao,
          prioridade: prioridade || 'MEDIA',
          dataInicio: new Date(dataInicio),
          dataFim: dataFim ? new Date(dataFim) : null,
          observacoes: observacoes || '',
          equipamentoId,
          setorId,
          userId: req.user!.id
        },
        include: {
          user: {
            select: {
              id: true,
              name: true,
              email: true
            }
          },
          setor: {
            select: {
              id: true,
              nome: true
            }
          },
          equipamento: {
            select: {
              id: true,
              nome: true,
              equipmentTag: true
            }
          },
          _count: {
            select: {
              imagens: true
            }
          }
        }
      });

      res.status(201).json(successResponse(relatorio, 'Relatório criado com sucesso'));

    } catch (error) {
      next(error);
    }
  }

  // Atualizar relatório
  static async updateRelatorio(req: Request, res: Response, next: NextFunction) {
    try {
      const { id } = req.params;

      // Validar dados de entrada
      const { error, value } = updateRelatorioSchema.validate(req.body);
      if (error) {
        return next(createError(error.details[0].message, 400));
      }

      const { 
        titulo, 
        descricao, 
        status, 
        prioridade, 
        dataInicio, 
        dataFim, 
        observacoes, 
        equipamentoId, 
        setorId 
      }: UpdateRelatorioDto = value;

      // Verificar se o relatório existe
      const existingRelatorio = await prisma.relatorio.findUnique({
        where: { id }
      });

      if (!existingRelatorio) {
        return next(createError('Relatório não encontrado', 404));
      }

      // Verificar permissão de edição
      if (req.user?.role !== 'ADMIN' && req.user?.role !== 'MANAGER' && existingRelatorio.userId !== req.user?.id) {
        return next(createError('Acesso negado', 403));
      }

      // Verificar se o equipamento existe (se fornecido)
      if (equipamentoId) {
        const equipamento = await prisma.equipamento.findUnique({
          where: { id: equipamentoId }
        });

        if (!equipamento) {
          return next(createError('Equipamento não encontrado', 404));
        }
      }

      // Verificar se o setor existe (se fornecido)
      if (setorId) {
        const setor = await prisma.setor.findUnique({
          where: { id: setorId }
        });

        if (!setor) {
          return next(createError('Setor não encontrado', 404));
        }

        if (!setor.active) {
          return next(createError('Setor está inativo', 400));
        }
      }

      // Preparar dados para atualização
      const updateData: any = {};
      if (titulo !== undefined) updateData.titulo = titulo;
      if (descricao !== undefined) updateData.descricao = descricao;
      if (status !== undefined) updateData.status = status;
      if (prioridade !== undefined) updateData.prioridade = prioridade;
      if (dataInicio !== undefined) updateData.dataInicio = new Date(dataInicio);
      if (dataFim !== undefined) updateData.dataFim = dataFim ? new Date(dataFim) : null;
      if (observacoes !== undefined) updateData.observacoes = observacoes;
      if (equipamentoId !== undefined) updateData.equipamentoId = equipamentoId;
      if (setorId !== undefined) updateData.setorId = setorId;

      // Atualizar relatório
      const relatorio = await prisma.relatorio.update({
        where: { id },
        data: updateData,
        include: {
          user: {
            select: {
              id: true,
              name: true,
              email: true
            }
          },
          setor: {
            select: {
              id: true,
              nome: true
            }
          },
          equipamento: {
            select: {
              id: true,
              nome: true,
              equipmentTag: true
            }
          },
          imagens: {
            select: {
              id: true,
              filename: true,
              originalName: true,
              path: true
            }
          },
          _count: {
            select: {
              imagens: true
            }
          }
        }
      });

      res.json(successResponse(relatorio, 'Relatório atualizado com sucesso'));

    } catch (error) {
      next(error);
    }
  }

  // Deletar relatório
  static async deleteRelatorio(req: Request, res: Response, next: NextFunction) {
    try {
      const { id } = req.params;

      // Verificar se o relatório existe
      const existingRelatorio = await prisma.relatorio.findUnique({
        where: { id },
        include: {
          imagens: true
        }
      });

      if (!existingRelatorio) {
        return next(createError('Relatório não encontrado', 404));
      }

      // Verificar permissão de exclusão
      if (req.user?.role !== 'ADMIN' && req.user?.role !== 'MANAGER' && existingRelatorio.userId !== req.user?.id) {
        return next(createError('Acesso negado', 403));
      }

      // Deletar relatório (cascade delete irá remover as imagens)
      await prisma.relatorio.delete({
        where: { id }
      });

      res.json(successResponse(null, 'Relatório deletado com sucesso'));

    } catch (error) {
      next(error);
    }
  }

  // Obter relatórios do usuário logado
  static async getMyRelatorios(req: Request, res: Response, next: NextFunction) {
    try {
      // Validar parâmetros de paginação
      const { error, value } = paginationSchema.validate(req.query);
      if (error) {
         return next(createError(error.details[0].message, 400));
       }

      const params = getPaginationParams(value);
      const { page = 1, limit = 10, search, sortBy = 'createdAt', sortOrder = 'desc' } = params;
      const { status, prioridade } = req.query;

      // Construir filtros de busca
      const where: any = {
        userId: req.user!.id
      };
      
      if (search) {
        where.OR = [
          { titulo: { contains: search, mode: 'insensitive' } },
          { descricao: { contains: search, mode: 'insensitive' } }
        ];
      }

      if (status) {
        where.status = status;
      }

      if (prioridade) {
        where.prioridade = prioridade;
      }

      // Contar total de registros
      const total = await prisma.relatorio.count({ where });

      // Buscar relatórios
      const relatorios = await prisma.relatorio.findMany({
        where,
        include: {
          setor: {
            select: {
              id: true,
              nome: true
            }
          },
          equipamento: {
            select: {
              id: true,
              nome: true,
              equipmentTag: true
            }
          },
          _count: {
            select: {
              imagens: true
            }
          }
        },
        orderBy: { [sortBy as string]: sortOrder },
        skip: (page - 1) * limit,
        take: limit
      });

      const response = createPaginatedResponse(relatorios, total, params);
      res.json(successResponse(response, 'Meus relatórios obtidos com sucesso'));

    } catch (error) {
      next(error);
    }
  }

  // Obter estatísticas de relatórios
  static async getRelatorioStats(req: Request, res: Response, next: NextFunction) {
    try {
      const userId = req.user?.role === 'ADMIN' || req.user?.role === 'MANAGER' ? undefined : req.user?.id;

      const where = userId ? { userId } : {};

      const [
        total,
        pendente,
        emAndamento,
        concluido,
        cancelado,
        baixa,
        media,
        alta,
        critica
      ] = await Promise.all([
        prisma.relatorio.count({ where }),
        prisma.relatorio.count({ where: { ...where, status: 'PENDENTE' } }),
        prisma.relatorio.count({ where: { ...where, status: 'EM_ANDAMENTO' } }),
        prisma.relatorio.count({ where: { ...where, status: 'CONCLUIDO' } }),
        prisma.relatorio.count({ where: { ...where, status: 'CANCELADO' } }),
        prisma.relatorio.count({ where: { ...where, prioridade: 'BAIXA' } }),
        prisma.relatorio.count({ where: { ...where, prioridade: 'MEDIA' } }),
        prisma.relatorio.count({ where: { ...where, prioridade: 'ALTA' } }),
        prisma.relatorio.count({ where: { ...where, prioridade: 'CRITICA' } })
      ]);

      const stats = {
        total,
        byStatus: {
          pendente,
          emAndamento,
          concluido,
          cancelado
        },
        byPrioridade: {
          baixa,
          media,
          alta,
          critica
        }
      };

      res.json(successResponse(stats, 'Estatísticas obtidas com sucesso'));

    } catch (error) {
      next(error);
    }
  }
}