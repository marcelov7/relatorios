import Joi from 'joi';

// Validações para User
export const createUserSchema = Joi.object({
  email: Joi.string().email().required().messages({
    'string.email': 'Email deve ter um formato válido',
    'any.required': 'Email é obrigatório'
  }),
  username: Joi.string().min(3).max(50).required().messages({
    'string.min': 'Nome de usuário deve ter pelo menos 3 caracteres',
    'string.max': 'Nome de usuário deve ter no máximo 50 caracteres',
    'any.required': 'Nome de usuário é obrigatório'
  }),
  name: Joi.string().min(2).max(100).required().messages({
    'string.min': 'Nome deve ter pelo menos 2 caracteres',
    'string.max': 'Nome deve ter no máximo 100 caracteres',
    'any.required': 'Nome é obrigatório'
  }),
  password: Joi.string().min(6).required().messages({
    'string.min': 'Senha deve ter pelo menos 6 caracteres',
    'any.required': 'Senha é obrigatória'
  }),
  role: Joi.string().valid('ADMIN', 'USER', 'MANAGER').default('USER')
});

export const updateUserSchema = Joi.object({
  email: Joi.string().email().messages({
    'string.email': 'Email deve ter um formato válido'
  }),
  username: Joi.string().min(3).max(50).messages({
    'string.min': 'Nome de usuário deve ter pelo menos 3 caracteres',
    'string.max': 'Nome de usuário deve ter no máximo 50 caracteres'
  }),
  name: Joi.string().min(2).max(100).messages({
    'string.min': 'Nome deve ter pelo menos 2 caracteres',
    'string.max': 'Nome deve ter no máximo 100 caracteres'
  }),
  password: Joi.string().min(6).messages({
    'string.min': 'Senha deve ter pelo menos 6 caracteres'
  }),
  role: Joi.string().valid('ADMIN', 'USER', 'MANAGER'),
  active: Joi.boolean()
});

// Validações para Setor
export const createSetorSchema = Joi.object({
  nome: Joi.string().min(2).max(100).required().messages({
    'string.min': 'Nome deve ter pelo menos 2 caracteres',
    'string.max': 'Nome deve ter no máximo 100 caracteres',
    'any.required': 'Nome é obrigatório'
  }),
  descricao: Joi.string().max(500).allow('').messages({
    'string.max': 'Descrição deve ter no máximo 500 caracteres'
  })
});

export const updateSetorSchema = Joi.object({
  nome: Joi.string().min(2).max(100).messages({
    'string.min': 'Nome deve ter pelo menos 2 caracteres',
    'string.max': 'Nome deve ter no máximo 100 caracteres'
  }),
  descricao: Joi.string().max(500).allow('').messages({
    'string.max': 'Descrição deve ter no máximo 500 caracteres'
  }),
  ativo: Joi.boolean()
});

// Validações para Equipamento
export const createEquipamentoSchema = Joi.object({
  nome: Joi.string().min(2).max(100).required().messages({
    'string.min': 'Nome deve ter pelo menos 2 caracteres',
    'string.max': 'Nome deve ter no máximo 100 caracteres',
    'any.required': 'Nome é obrigatório'
  }),
  modelo: Joi.string().max(100).allow('').messages({
    'string.max': 'Modelo deve ter no máximo 100 caracteres'
  }),
  numeroSerie: Joi.string().min(1).max(50).required().messages({
    'string.min': 'Número de série é obrigatório',
    'string.max': 'Número de série deve ter no máximo 50 caracteres',
    'any.required': 'Número de série é obrigatório'
  }),
  localizacao: Joi.string().max(200).allow('').messages({
    'string.max': 'Localização deve ter no máximo 200 caracteres'
  }),
  status: Joi.string().valid('ATIVO', 'INATIVO', 'MANUTENCAO', 'DESCARTADO').default('ATIVO'),
  setorId: Joi.string().required().messages({
    'any.required': 'Setor é obrigatório'
  })
});

export const updateEquipamentoSchema = Joi.object({
  nome: Joi.string().min(2).max(100).messages({
    'string.min': 'Nome deve ter pelo menos 2 caracteres',
    'string.max': 'Nome deve ter no máximo 100 caracteres'
  }),
  modelo: Joi.string().max(100).allow('').messages({
    'string.max': 'Modelo deve ter no máximo 100 caracteres'
  }),
  numeroSerie: Joi.string().min(1).max(50).messages({
    'string.min': 'Número de série é obrigatório',
    'string.max': 'Número de série deve ter no máximo 50 caracteres'
  }),
  localizacao: Joi.string().max(200).allow('').messages({
    'string.max': 'Localização deve ter no máximo 200 caracteres'
  }),
  status: Joi.string().valid('ATIVO', 'INATIVO', 'MANUTENCAO', 'DESCARTADO'),
  setorId: Joi.string()
});

// Validações para Relatório
export const createRelatorioSchema = Joi.object({
  titulo: Joi.string().min(5).max(200).required().messages({
    'string.min': 'Título deve ter pelo menos 5 caracteres',
    'string.max': 'Título deve ter no máximo 200 caracteres',
    'any.required': 'Título é obrigatório'
  }),
  descricao: Joi.string().min(10).required().messages({
    'string.min': 'Descrição deve ter pelo menos 10 caracteres',
    'any.required': 'Descrição é obrigatória'
  }),
  prioridade: Joi.string().valid('BAIXA', 'MEDIA', 'ALTA', 'CRITICA').default('MEDIA'),
  dataInicio: Joi.date().iso().required().messages({
    'date.format': 'Data de início deve estar no formato ISO',
    'any.required': 'Data de início é obrigatória'
  }),
  dataFim: Joi.date().iso().min(Joi.ref('dataInicio')).messages({
    'date.format': 'Data de fim deve estar no formato ISO',
    'date.min': 'Data de fim deve ser posterior à data de início'
  }),
  observacoes: Joi.string().max(1000).allow('').messages({
    'string.max': 'Observações devem ter no máximo 1000 caracteres'
  }),
  equipamentoId: Joi.string().required().messages({
    'any.required': 'Equipamento é obrigatório'
  }),
  setorId: Joi.string().required().messages({
    'any.required': 'Setor é obrigatório'
  })
});

export const updateRelatorioSchema = Joi.object({
  titulo: Joi.string().min(5).max(200).messages({
    'string.min': 'Título deve ter pelo menos 5 caracteres',
    'string.max': 'Título deve ter no máximo 200 caracteres'
  }),
  descricao: Joi.string().min(10).messages({
    'string.min': 'Descrição deve ter pelo menos 10 caracteres'
  }),
  status: Joi.string().valid('PENDENTE', 'EM_ANDAMENTO', 'CONCLUIDO', 'CANCELADO'),
  prioridade: Joi.string().valid('BAIXA', 'MEDIA', 'ALTA', 'CRITICA'),
  dataInicio: Joi.date().iso().messages({
    'date.format': 'Data de início deve estar no formato ISO'
  }),
  dataFim: Joi.date().iso().min(Joi.ref('dataInicio')).messages({
    'date.format': 'Data de fim deve estar no formato ISO',
    'date.min': 'Data de fim deve ser posterior à data de início'
  }),
  observacoes: Joi.string().max(1000).allow('').messages({
    'string.max': 'Observações devem ter no máximo 1000 caracteres'
  }),
  equipamentoId: Joi.string(),
  setorId: Joi.string()
});

// Validações para Login
export const loginSchema = Joi.object({
  login: Joi.string().required().messages({
    'any.required': 'Nome de usuário ou email é obrigatório'
  }),
  senha: Joi.string().required().messages({
    'any.required': 'Senha é obrigatória'
  })
});

// Validações para paginação
export const paginationSchema = Joi.object({
  page: Joi.number().integer().min(1).default(1),
  limit: Joi.number().integer().min(1).max(100).default(10),
  search: Joi.string().allow(''),
  sortBy: Joi.string().allow(''),
  sortOrder: Joi.string().valid('asc', 'desc').default('desc')
});