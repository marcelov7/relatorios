import * as yup from 'yup';
import { UserRole, EquipamentoStatus, RelatorioStatus, Prioridade } from '@prisma/client';

// Esquema de validação para criação de usuário
export const createUserSchema = yup.object().shape({
  username: yup.string()
    .required('Username é obrigatório')
    .min(3, 'Username deve ter no mínimo 3 caracteres')
    .max(50, 'Username deve ter no máximo 50 caracteres'),
  name: yup.string()
    .required('Nome é obrigatório')
    .min(3, 'Nome deve ter no mínimo 3 caracteres')
    .max(100, 'Nome deve ter no máximo 100 caracteres'),
  email: yup.string()
    .required('Email é obrigatório')
    .email('Email inválido'),
  password: yup.string()
    .required('Senha é obrigatória')
    .min(6, 'Senha deve ter no mínimo 6 caracteres'),
  role: yup.mixed<UserRole>()
    .oneOf(Object.values(UserRole), 'Papel inválido')
    .required('Papel é obrigatório')
});

// Esquema de validação para atualização de usuário
export const updateUserSchema = yup.object().shape({
  username: yup.string()
    .min(3, 'Username deve ter no mínimo 3 caracteres')
    .max(50, 'Username deve ter no máximo 50 caracteres'),
  name: yup.string()
    .min(3, 'Nome deve ter no mínimo 3 caracteres')
    .max(100, 'Nome deve ter no máximo 100 caracteres'),
  email: yup.string()
    .email('Email inválido'),
  password: yup.string()
    .min(6, 'Senha deve ter no mínimo 6 caracteres'),
  role: yup.mixed<UserRole>()
    .oneOf(Object.values(UserRole), 'Papel inválido')
});

// Esquema de validação para criação de setor
export const createSetorSchema = yup.object().shape({
  nome: yup.string()
    .required('Nome é obrigatório')
    .min(3, 'Nome deve ter no mínimo 3 caracteres')
    .max(100, 'Nome deve ter no máximo 100 caracteres'),
  descricao: yup.string()
    .required('Descrição é obrigatória')
    .max(500, 'Descrição deve ter no máximo 500 caracteres')
});

// Esquema de validação para atualização de setor
export const updateSetorSchema = yup.object().shape({
  nome: yup.string()
    .min(3, 'Nome deve ter no mínimo 3 caracteres')
    .max(100, 'Nome deve ter no máximo 100 caracteres'),
  descricao: yup.string()
    .max(500, 'Descrição deve ter no máximo 500 caracteres')
});

// Esquema de validação para criação de equipamento
export const createEquipamentoSchema = yup.object().shape({
  nome: yup.string()
    .required('Nome é obrigatório')
    .min(3, 'Nome deve ter no mínimo 3 caracteres')
    .max(100, 'Nome deve ter no máximo 100 caracteres'),
  descricao: yup.string()
    .required('Descrição é obrigatória')
    .max(500, 'Descrição deve ter no máximo 500 caracteres'),
  setorId: yup.string()
    .required('Setor é obrigatório')
    .uuid('ID do setor inválido'),
  status: yup.mixed<EquipamentoStatus>()
    .oneOf(Object.values(EquipamentoStatus), 'Status inválido')
    .required('Status é obrigatório')
});

// Esquema de validação para atualização de equipamento
export const updateEquipamentoSchema = yup.object().shape({
  nome: yup.string()
    .min(3, 'Nome deve ter no mínimo 3 caracteres')
    .max(100, 'Nome deve ter no máximo 100 caracteres'),
  descricao: yup.string()
    .max(500, 'Descrição deve ter no máximo 500 caracteres'),
  setorId: yup.string()
    .uuid('ID do setor inválido'),
  status: yup.mixed<EquipamentoStatus>()
    .oneOf(Object.values(EquipamentoStatus), 'Status inválido')
});

// Esquema de validação para criação de relatório
export const createRelatorioSchema = yup.object().shape({
  titulo: yup.string()
    .required('Título é obrigatório')
    .min(3, 'Título deve ter no mínimo 3 caracteres')
    .max(100, 'Título deve ter no máximo 100 caracteres'),
  descricao: yup.string()
    .required('Descrição é obrigatória')
    .max(1000, 'Descrição deve ter no máximo 1000 caracteres'),
  equipamentoId: yup.string()
    .required('Equipamento é obrigatório')
    .uuid('ID do equipamento inválido'),
  status: yup.mixed<RelatorioStatus>()
    .oneOf(Object.values(RelatorioStatus), 'Status inválido')
    .required('Status é obrigatório'),
  prioridade: yup.mixed<Prioridade>()
    .oneOf(Object.values(Prioridade), 'Prioridade inválida')
    .required('Prioridade é obrigatória')
});

// Esquema de validação para atualização de relatório
export const updateRelatorioSchema = yup.object().shape({
  titulo: yup.string()
    .min(3, 'Título deve ter no mínimo 3 caracteres')
    .max(100, 'Título deve ter no máximo 100 caracteres'),
  descricao: yup.string()
    .max(1000, 'Descrição deve ter no máximo 1000 caracteres'),
  equipamentoId: yup.string()
    .uuid('ID do equipamento inválido'),
  status: yup.mixed<RelatorioStatus>()
    .oneOf(Object.values(RelatorioStatus), 'Status inválido'),
  prioridade: yup.mixed<Prioridade>()
    .oneOf(Object.values(Prioridade), 'Prioridade inválida')
});

// Esquema de validação para login
export const loginSchema = yup.object().shape({
  username: yup.string()
    .required('Username ou email é obrigatório'),
  password: yup.string()
    .required('Senha é obrigatória')
});