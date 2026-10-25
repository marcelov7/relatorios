import { Request, Response, NextFunction } from 'express';
import prisma from '../config/database';
import { getPaginationParams, createPaginatedResponse, successResponse } from '../utils/helpers';
import { createEquipamentoSchema, updateEquipamentoSchema, paginationSchema } from '../utils/validation';
import { createError } from '../middleware/errorHandler';
import { CreateEquipamentoDto, UpdateEquipamentoDto } from '../types';

export class EquipamentoController {
  // Listar equipamentos com paginação e filtros
  static async getEquipamentos(req: Request, res: Response, next: NextFunction) {
    try {
      // Validar parâmetros de paginação
      const { error, value } = paginationSchema.validate(req.query);
      if (error) {
        return next(createError(error.details[0].message, 400));
      }

      const params = getPaginationParams(value);
      const { page = 1, limit = 10, search, sortBy = 'nome', sortOrder = 'asc' } = params;
      const { setorId, status } = req.query;

      // Construir filtros de busca
      const where: any = {};
      
      if (search) {
        where.OR = [
          { nome: { contains: search, mode: 'insensitive' } },
          { equipmentTag: { contains: search, mode: 'insensitive' } },
          { descricao: { contains: search, mode: 'insensitive' } }
        ];
      }

      if (setorId) {
        where.setorId = setorId;
      }

      if (status) {
        where.status = status;
      }

      // Contar total de registros
      const total = await prisma.equipamento.count({ where });

      // Buscar equipamentos
      const equipamentos = await prisma.equipamento.findMany({
        where,
        include: {
          setor: {
            select: {
              id: true,
              nome: true
            }
          },
          _count: {
            select: {
              relatorios: true
            }
          }
        },
        orderBy: { [sortBy as string]: sortOrder },
        skip: (page - 1) * limit,
        take: limit
      });

      const response = createPaginatedResponse(equipamentos, total, params);
      res.json(successResponse(response, 'Equipamentos obtidos com sucesso'));

    } catch (error) {
      next(error);
    }
  }

  // Obter equipamento por ID
  static async getEquipamentoById(req: Request, res: Response, next: NextFunction) {
    try {
      const { id } = req.params;

      const equipamento = await prisma.equipamento.findUnique({
        where: { id },
        include: {
          setor: {
            select: {
              id: true,
              nome: true,
              descricao: true
            }
          },
          relatorios: {
            select: {
              id: true,
              titulo: true,
              status: true,
              prioridade: true,
              dataInicio: true,
              dataFim: true,
              createdAt: true
            },
            orderBy: { createdAt: 'desc' },
            take: 10
          },
          _count: {
            select: {
              relatorios: true
            }
          }
        }
      });

      if (!equipamento) {
        return next(createError('Equipamento não encontrado', 404));
      }

      res.json(successResponse(equipamento, 'Equipamento obtido com sucesso'));

    } catch (error) {
      next(error);
    }
  }

  // Criar equipamento
  static async createEquipamento(req: Request, res: Response, next: NextFunction) {
    try {
      // Validar dados de entrada
      const { error, value } = createEquipamentoSchema.validate(req.body);
      if (error) {
        return next(createError(error.details[0].message, 400));
      }

      const { nome, modelo, numeroSerie, localizacao, status, setorId }: CreateEquipamentoDto = value;

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

      // Verificar se já existe um equipamento com o mesmo equipmentTag
      const existingEquipamento = await prisma.equipamento.findFirst({
        where: { 
          equipmentTag: { equals: modelo }
        }
      });

      if (existingEquipamento) {
        return next(createError('Já existe um equipamento com este equipmentTag', 409));
      }

      // Criar equipamento
      const equipamento = await prisma.equipamento.create({
        data: {
          nome,
          equipmentTag: modelo || '',
          descricao: localizacao || '',
          status: status || 'ATIVO',
          setorId
        },
        include: {
          setor: {
            select: {
              id: true,
              nome: true
            }
          },
          _count: {
            select: {
              relatorios: true
            }
          }
        }
      });

      res.status(201).json(successResponse(equipamento, 'Equipamento criado com sucesso'));

    } catch (error) {
      next(error);
    }
  }

  // Atualizar equipamento
  static async updateEquipamento(req: Request, res: Response, next: NextFunction) {
    try {
      const { id } = req.params;

      // Validar dados de entrada
      const { error, value } = updateEquipamentoSchema.validate(req.body);
      if (error) {
        return next(createError(error.details[0].message, 400));
      }

      const { nome, equipmentTag, descricao, status, setorId }: UpdateEquipamentoDto = value;

      // Verificar se o equipamento existe
      const existingEquipamento = await prisma.equipamento.findUnique({
        where: { id }
      });

      if (!existingEquipamento) {
        return next(createError('Equipamento não encontrado', 404));
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

      // Verificar se a tag já está em uso por outro equipamento
      if (equipmentTag && equipmentTag !== existingEquipamento.equipmentTag) {
        const tagExists = await prisma.equipamento.findFirst({
          where: { 
            equipmentTag: { equals: equipmentTag },
            id: { not: id }
          }
        });

        if (tagExists) {
          return next(createError('Tag já está em uso por outro equipamento', 409));
        }
      }

      // Preparar dados para atualização
      const updateData: any = {};
      if (nome !== undefined) updateData.nome = nome;
      if (equipmentTag !== undefined) updateData.equipmentTag = equipmentTag;
      if (descricao !== undefined) updateData.descricao = descricao;
      if (status !== undefined) updateData.status = status;
      if (setorId !== undefined) updateData.setorId = setorId;

      // Atualizar equipamento
      const equipamento = await prisma.equipamento.update({
        where: { id },
        data: updateData,
        include: {
          setor: {
            select: {
              id: true,
              nome: true
            }
          },
          _count: {
            select: {
              relatorios: true
            }
          }
        }
      });

      res.json(successResponse(equipamento, 'Equipamento atualizado com sucesso'));

    } catch (error) {
      next(error);
    }
  }

  // Deletar equipamento
  static async deleteEquipamento(req: Request, res: Response, next: NextFunction) {
    try {
      const { id } = req.params;

      // Verificar se o equipamento existe
      const existingEquipamento = await prisma.equipamento.findUnique({
        where: { id },
        include: {
          _count: {
            select: {
              relatorios: true
            }
          }
        }
      });

      if (!existingEquipamento) {
        return next(createError('Equipamento não encontrado', 404));
      }

      // Verificar se há relatórios associados
      if (existingEquipamento._count.relatorios > 0) {
        return next(createError('Não é possível deletar equipamento com relatórios associados', 400));
      }

      // Deletar equipamento
      await prisma.equipamento.delete({
        where: { id }
      });

      res.json(successResponse(null, 'Equipamento deletado com sucesso'));

    } catch (error) {
      next(error);
    }
  }

  // Obter equipamentos por setor
  static async getEquipamentosBySetor(req: Request, res: Response, next: NextFunction) {
    try {
      const { setorId } = req.params;

      // Verificar se o setor existe
      const setor = await prisma.setor.findUnique({
        where: { id: setorId }
      });

      if (!setor) {
        return next(createError('Setor não encontrado', 404));
      }

      const equipamentos = await prisma.equipamento.findMany({
        where: { 
          setorId,
          status: { not: 'DESCARTADO' }
        },
        select: {
          id: true,
          nome: true,
          equipmentTag: true,
          status: true,
          descricao: true
        },
        orderBy: { nome: 'asc' }
      });

      res.json(successResponse(equipamentos, 'Equipamentos do setor obtidos com sucesso'));

    } catch (error) {
      next(error);
    }
  }

  // Obter equipamentos ativos (para dropdowns)
  static async getActiveEquipamentos(req: Request, res: Response, next: NextFunction) {
    try {
      const equipamentos = await prisma.equipamento.findMany({
        where: { status: 'ATIVO' },
        select: {
          id: true,
          nome: true,
          equipmentTag: true,
          setor: {
            select: {
              id: true,
              nome: true
            }
          }
        },
        orderBy: { nome: 'asc' }
      });

      res.json(successResponse(equipamentos, 'Equipamentos ativos obtidos com sucesso'));

    } catch (error) {
      next(error);
    }
  }

  // Obter estatísticas de equipamentos
  static async getEquipamentoStats(req: Request, res: Response, next: NextFunction) {
    try {
      const [total, ativo, inativo, manutencao, descartado] = await Promise.all([
        prisma.equipamento.count(),
        prisma.equipamento.count({ where: { status: 'ATIVO' } }),
        prisma.equipamento.count({ where: { status: 'INATIVO' } }),
        prisma.equipamento.count({ where: { status: 'MANUTENCAO' } }),
        prisma.equipamento.count({ where: { status: 'DESCARTADO' } })
      ]);

      const stats = {
        total,
        byStatus: {
          ativo,
          inativo,
          manutencao,
          descartado
        }
      };

      res.json(successResponse(stats, 'Estatísticas obtidas com sucesso'));

    } catch (error) {
      next(error);
    }
  }
}