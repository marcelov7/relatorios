import { UserRole, EquipamentoStatus, RelatorioStatus, Prioridade } from '@prisma/client';

// Tipos para usuários
export interface CreateUserDto {
  username: string;
  name: string;
  email: string;
  password: string;
  role?: UserRole;
  active?: boolean;
}

export interface UpdateUserDto {
  username?: string;
  name?: string;
  email?: string;
  password?: string;
  role?: UserRole;
  active?: boolean;
}

// Tipos para setores
export interface CreateSetorDto {
  nome: string;
  descricao?: string;
  ativo?: boolean;
}

export interface UpdateSetorDto {
  nome?: string;
  descricao?: string;
  ativo?: boolean;
}

// Tipos para equipamentos
export interface CreateEquipamentoDto {
  nome: string;
  modelo?: string;
  numeroSerie: string;
  localizacao?: string;
  status?: EquipamentoStatus;
  setorId: string;
}

export interface UpdateEquipamentoDto {
  nome?: string;
  modelo?: string;
  numeroSerie?: string;
  localizacao?: string;
  status?: EquipamentoStatus;
  setorId?: string;
}

// Tipos para relatórios
export interface CreateRelatorioDto {
  titulo: string;
  descricao: string;
  status?: RelatorioStatus;
  prioridade?: Prioridade;
  dataInicio?: Date;
  dataConclusao?: Date;
  observacoes?: string;
  autorId: string;
  revisorId?: string;
  equipamentoId: string;
  setorId: string;
}

export interface UpdateRelatorioDto {
  titulo?: string;
  descricao?: string;
  status?: RelatorioStatus;
  prioridade?: Prioridade;
  dataConclusao?: Date;
  observacoes?: string;
  revisorId?: string;
}

// Tipos para autenticação
export interface LoginDto {
  email: string;
  password: string;
}

export interface RegisterDto {
  username: string;
  name: string;
  email: string;
  password: string;
}

// Tipos para paginação
export interface PaginationDto {
  page?: number;
  limit?: number;
  search?: string;
  sortBy?: string;
  sortOrder?: 'asc' | 'desc';
}

export interface PaginatedResponse<T> {
  items: T[];
  total: number;
  page: number;
  limit: number;
  totalPages: number;
}

// Tipos para respostas de erro
export interface ErrorResponse {
  message: string;
  errors?: Record<string, string[]>;
  stack?: string;
}