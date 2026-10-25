import { Request, Response } from 'express';
import { PrismaClient } from '@prisma/client';
import bcrypt from 'bcryptjs';
import { createUserSchema, updateUserSchema } from '../utils/validation';
import { CreateUserDto, UpdateUserDto } from '../types';

const prisma = new PrismaClient();

export const createUser = async (req: Request, res: Response) => {
  try {
    const { error, value } = createUserSchema.validate(req.body);
    if (error) {
      return res.status(400).json({ message: error.details[0].message });
    }

    const { email, username, name, password, role } = value as CreateUserDto;

    // Verifica se já existe um usuário com o mesmo email
    const existingUserEmail = await prisma.user.findUnique({ where: { email } });
    if (existingUserEmail) {
      return res.status(400).json({ message: 'Email já está em uso' });
    }

    // Verifica se já existe um usuário com o mesmo username
    const existingUserUsername = await prisma.user.findUnique({ where: { username } });
    if (existingUserUsername) {
      return res.status(400).json({ message: 'Nome de usuário já está em uso' });
    }

    // Hash da senha
    const hashedPassword = await bcrypt.hash(password, 10);

    // Cria o usuário
    const user = await prisma.user.create({
      data: {
        email,
        username,
        name,
        password: hashedPassword,
        role: role || 'USER',
        active: true
      }
    });

    // Remove a senha do objeto de resposta
    const { password: _, ...userWithoutPassword } = user;

    return res.status(201).json(userWithoutPassword);
  } catch (error) {
    console.error('Erro ao criar usuário:', error);
    return res.status(500).json({ message: 'Erro interno do servidor' });
  }
};

export const updateUser = async (req: Request, res: Response) => {
  try {
    const { id } = req.params;
    const { error, value } = updateUserSchema.validate(req.body);
    if (error) {
      return res.status(400).json({ message: error.details[0].message });
    }

    const updateData = value as UpdateUserDto;

    // Se houver uma nova senha, faz o hash
    if (updateData.password) {
      updateData.password = await bcrypt.hash(updateData.password, 10);
    }

    // Se houver um novo email, verifica se já está em uso
    if (updateData.email) {
      const existingUser = await prisma.user.findFirst({
        where: {
          email: updateData.email,
          id: { not: id }
        }
      });
      if (existingUser) {
        return res.status(400).json({ message: 'Email já está em uso' });
      }
    }

    // Se houver um novo username, verifica se já está em uso
    if (updateData.username) {
      const existingUser = await prisma.user.findFirst({
        where: {
          username: updateData.username,
          id: { not: id }
        }
      });
      if (existingUser) {
        return res.status(400).json({ message: 'Nome de usuário já está em uso' });
      }
    }

    // Atualiza o usuário
    const user = await prisma.user.update({
      where: { id },
      data: updateData
    });

    // Remove a senha do objeto de resposta
    const { password: _, ...userWithoutPassword } = user;

    return res.json(userWithoutPassword);
  } catch (error) {
    console.error('Erro ao atualizar usuário:', error);
    if (error.code === 'P2025') {
      return res.status(404).json({ message: 'Usuário não encontrado' });
    }
    return res.status(500).json({ message: 'Erro interno do servidor' });
  }
};

export const deleteUser = async (req: Request, res: Response) => {
  try {
    const { id } = req.params;

    await prisma.user.delete({
      where: { id }
    });

    return res.status(204).send();
  } catch (error) {
    console.error('Erro ao excluir usuário:', error);
    if (error.code === 'P2025') {
      return res.status(404).json({ message: 'Usuário não encontrado' });
    }
    return res.status(500).json({ message: 'Erro interno do servidor' });
  }
};

export const getUser = async (req: Request, res: Response) => {
  try {
    const { id } = req.params;

    const user = await prisma.user.findUnique({
      where: { id }
    });

    if (!user) {
      return res.status(404).json({ message: 'Usuário não encontrado' });
    }

    // Remove a senha do objeto de resposta
    const { password: _, ...userWithoutPassword } = user;

    return res.json(userWithoutPassword);
  } catch (error) {
    console.error('Erro ao buscar usuário:', error);
    return res.status(500).json({ message: 'Erro interno do servidor' });
  }
};

export const listUsers = async (req: Request, res: Response) => {
  try {
    const { page = 1, limit = 10, search = '', sortBy = 'createdAt', sortOrder = 'desc' } = req.query;

    // Constrói o objeto de ordenação
    const orderBy = {
      [String(sortBy)]: sortOrder
    };

    // Constrói o objeto de filtro
    const where = search
      ? {
          OR: [
            { name: { contains: String(search), mode: 'insensitive' } },
            { email: { contains: String(search), mode: 'insensitive' } },
            { username: { contains: String(search), mode: 'insensitive' } }
          ]
        }
      : {};

    // Calcula o número de registros a pular
    const skip = (Number(page) - 1) * Number(limit);

    // Busca os usuários com paginação
    const [users, total] = await Promise.all([
      prisma.user.findMany({
        where,
        orderBy,
        skip,
        take: Number(limit),
        select: {
          id: true,
          email: true,
          username: true,
          name: true,
          role: true,
          active: true,
          createdAt: true,
          updatedAt: true
        }
      }),
      prisma.user.count({ where })
    ]);

    // Calcula o total de páginas
    const totalPages = Math.ceil(total / Number(limit));

    return res.json({
      items: users,
      total,
      page: Number(page),
      limit: Number(limit),
      totalPages
    });
  } catch (error) {
    console.error('Erro ao listar usuários:', error);
    return res.status(500).json({ message: 'Erro interno do servidor' });
  }
};