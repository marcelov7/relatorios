import { Request, Response, NextFunction } from 'express';
import prisma from '../config/database';
import { getPaginationParams, createPaginatedResponse, successResponse } from '../utils/helpers';
import { createSetorSchema, updateSetorSchema, paginationSchema } from '../utils/validation';
import { createError } from '../middleware/errorHandler';
import { CreateSetorDto, UpdateSetorDto } from '../types';

export class SetorController {
  // Listar setores com paginação e filtros
  static async getSetores(req: Request, res: Response, next: NextFunction) {
    try {
      // Validar parâmetros de paginação
      const { error, value } = paginationSchema.validate(req.query);
      if (error) {
        return next(createError(error.details[0].message, 400));
      }

      const params = getPaginationParams(value);
      const { page = 1, limit = 10, search, sortBy = 'nome', sortOrder = 'asc' } = params;

      // Construir filtros de busca
      const where: any = {};
      if (search) {
        where.OR = [
          { nome: { contains: search, mode: 'insensitive' } },
          { descricao: { contains: search, mode: 'insensitive' } }
        ];
      }

      // Contar total de registros
      const total = await prisma.setor.count({ where });

      // Buscar setores
      const setores = await prisma.setor.findMany({
        where,
        include: {
          _count: {
            select: {
              equipamentos: true,
              relatorios: true
            }
          }
        },
        orderBy: { [sortBy as string]: sortOrder },
        skip: (page - 1) * limit,
        take: limit
      });

      const response = createPaginatedResponse(setores, total, params);
      res.json(successResponse(response, 'Setores obtidos com sucesso'));

    } catch (error) {
      next(error);
    }
  }

  // Obter setor por ID
  static async getSetorById(req: Request, res: Response, next: NextFunction) {
    try {
      const { id } = req.params;

      const setor = await prisma.setor.findUnique({
        where: { id },
        include: {
          equipamentos: {
            select: {
              id: true,
              nome: true,
              equipmentTag: true,
              status: true,
              createdAt: true
            }
          },
          _count: {
            select: {
              equipamentos: true,
              relatorios: true
            }
          }
        }
      });

      if (!setor) {
        return next(createError('Setor não encontrado', 404));
      }

      res.json(successResponse(setor, 'Setor obtido com sucesso'));

    } catch (error) {
      next(error);
    }
  }

  // Criar setor
  static async createSetor(req: Request, res: Response, next: NextFunction) {
    try {
      // Validar dados de entrada
      const { error, value } = createSetorSchema.validate(req.body);
      if (error) {
        return next(createError(error.details[0].message, 400));
      }

      const { nome, descricao }: CreateSetorDto = value;

      // Verificar se já existe um setor com o mesmo nome
      const existingSetor = await prisma.setor.findFirst({
        where: { 
          nome: { equals: nome }
        }
      });

      if (existingSetor) {
        return next(createError('Já existe um setor com este nome', 409));
      }

      // Criar setor
      const setor = await prisma.setor.create({
        data: {
          nome,
          descricao: descricao || ''
        },
        include: {
          _count: {
            select: {
              equipamentos: true,
              relatorios: true
            }
          }
        }
      });

      res.status(201).json(successResponse(setor, 'Setor criado com sucesso'));

    } catch (error) {
      next(error);
    }
  }

  // Atualizar setor
  static async updateSetor(req: Request, res: Response, next: NextFunction) {
    try {
      const { id } = req.params;

      // Validar dados de entrada
      const { error, value } = updateSetorSchema.validate(req.body);
      if (error) {
        return next(createError(error.details[0].message, 400));
      }

      const { nome, descricao, active }: UpdateSetorDto = value;

      // Verificar se o setor existe
      const existingSetor = await prisma.setor.findUnique({
        where: { id }
      });

      if (!existingSetor) {
        return next(createError('Setor não encontrado', 404));
      }

      // Verificar se o nome já está em uso por outro setor
      if (nome && nome !== existingSetor.nome) {
        const nomeExists = await prisma.setor.findFirst({
          where: { 
            nome: { equals: nome },
            id: { not: id }
          }
        });

        if (nomeExists) {
          return next(createError('Nome já está em uso por outro setor', 409));
        }
      }

      // Preparar dados para atualização
      const updateData: any = {};
      if (nome !== undefined) updateData.nome = nome;
      if (descricao !== undefined) updateData.descricao = descricao;
      if (active !== undefined) updateData.active = active;

      // Atualizar setor
      const setor = await prisma.setor.update({
        where: { id },
        data: updateData,
        include: {
          _count: {
            select: {
              equipamentos: true,
              relatorios: true
            }
          }
        }
      });

      res.json(successResponse(setor, 'Setor atualizado com sucesso'));

    } catch (error) {
      next(error);
    }
  }

  // Deletar setor (soft delete)
  static async deleteSetor(req: Request, res: Response, next: NextFunction) {
    try {
      const { id } = req.params;

      // Verificar se o setor existe
      const existingSetor = await prisma.setor.findUnique({
        where: { id },
        include: {
          _count: {
            select: {
              equipamentos: true,
              relatorios: true
            }
          }
        }
      });

      if (!existingSetor) {
        return next(createError('Setor não encontrado', 404));
      }

      // Verificar se há equipamentos ou relatórios associados
      if (existingSetor._count.equipamentos > 0) {
        return next(createError('Não é possível deletar setor com equipamentos associados', 400));
      }

      if (existingSetor._count.relatorios > 0) {
        return next(createError('Não é possível deletar setor com relatórios associados', 400));
      }

      // Soft delete - desativar setor
      const setor = await prisma.setor.update({
        where: { id },
        data: { active: false },
        include: {
          _count: {
            select: {
              equipamentos: true,
              relatorios: true
            }
          }
        }
      });

      res.json(successResponse(setor, 'Setor desativado com sucesso'));

    } catch (error) {
      next(error);
    }
  }

  // Ativar setor
  static async activateSetor(req: Request, res: Response, next: NextFunction) {
    try {
      const { id } = req.params;

      // Verificar se o setor existe
      const existingSetor = await prisma.setor.findUnique({
        where: { id }
      });

      if (!existingSetor) {
        return next(createError('Setor não encontrado', 404));
      }

      // Ativar setor
      const setor = await prisma.setor.update({
        where: { id },
        data: { active: true },
        include: {
          _count: {
            select: {
              equipamentos: true,
              relatorios: true
            }
          }
        }
      });

      res.json(successResponse(setor, 'Setor ativado com sucesso'));

    } catch (error) {
      next(error);
    }
  }

  // Obter setores ativos (para dropdowns)
  static async getActiveSetores(req: Request, res: Response, next: NextFunction) {
    try {
      const setores = await prisma.setor.findMany({
        where: { active: true },
        select: {
          id: true,
          nome: true,
          descricao: true
        },
        orderBy: { nome: 'asc' }
      });

      res.json(successResponse(setores, 'Setores ativos obtidos com sucesso'));

    } catch (error) {
      next(error);
    }
  }

  // Obter estatísticas de setores
  static async getSetorStats(req: Request, res: Response, next: NextFunction) {
    try {
      const [total, active, inactive] = await Promise.all([
        prisma.setor.count(),
        prisma.setor.count({ where: { active: true } }),
        prisma.setor.count({ where: { active: false } })
      ]);

      const stats = {
        total,
        active,
        inactive
      };

      res.json(successResponse(stats, 'Estatísticas obtidas com sucesso'));

    } catch (error) {
      next(error);
    }
  }
}