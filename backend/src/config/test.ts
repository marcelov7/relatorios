import { PrismaClient } from '@prisma/client';
import { hashPassword } from './auth';

// Cliente Prisma para testes
export const prisma = new PrismaClient();

// Dados de teste para usuários
export const testUsers = {
  admin: {
    username: 'admin',
    name: 'Administrador',
    email: 'admin@test.com',
    password: 'admin123',
    role: 'ADMIN'
  },
  manager: {
    username: 'manager',
    name: 'Gerente',
    email: 'manager@test.com',
    password: 'manager123',
    role: 'MANAGER'
  },
  user: {
    username: 'user',
    name: 'Usuário',
    email: 'user@test.com',
    password: 'user123',
    role: 'USER'
  }
};

// Dados de teste para setores
export const testSetores = [
  {
    nome: 'Setor de Teste 1',
    descricao: 'Descrição do setor de teste 1'
  },
  {
    nome: 'Setor de Teste 2',
    descricao: 'Descrição do setor de teste 2'
  }
];

// Dados de teste para equipamentos
export const testEquipamentos = [
  {
    nome: 'Equipamento de Teste 1',
    descricao: 'Descrição do equipamento de teste 1',
    status: 'ATIVO'
  },
  {
    nome: 'Equipamento de Teste 2',
    descricao: 'Descrição do equipamento de teste 2',
    status: 'INATIVO'
  }
];

// Função para limpar o banco de dados de teste
export async function clearDatabase() {
  const tablenames = await prisma.$queryRaw<
    Array<{ tablename: string }>
  >`SELECT tablename FROM pg_tables WHERE schemaname='public'`;

  const tables = tablenames
    .map(({ tablename }) => tablename)
    .filter((name) => name !== '_prisma_migrations')
    .map((name) => `"public"."${name}"`)
    .join(', ');

  try {
    await prisma.$executeRawUnsafe(`TRUNCATE TABLE ${tables} CASCADE;`);
  } catch (error) {
    console.log('Erro ao limpar o banco de dados:', error);
  }
}

// Função para criar dados de teste
export async function seedTestData() {
  try {
    // Cria usuários de teste
    for (const [_, userData] of Object.entries(testUsers)) {
      const hashedPassword = await hashPassword(userData.password);
      await prisma.user.create({
        data: {
          ...userData,
          password: hashedPassword
        }
      });
    }

    // Cria setores de teste
    const setores = await Promise.all(
      testSetores.map((setor) =>
        prisma.setor.create({
          data: setor
        })
      )
    );

    // Cria equipamentos de teste
    await Promise.all(
      testEquipamentos.map((equipamento, index) =>
        prisma.equipamento.create({
          data: {
            ...equipamento,
            setorId: setores[index % setores.length].id
          }
        })
      )
    );
  } catch (error) {
    console.log('Erro ao criar dados de teste:', error);
  }
}

// Função para configurar o ambiente de teste
export async function setupTestEnvironment() {
  try {
    await clearDatabase();
    await seedTestData();
  } catch (error) {
    console.log('Erro ao configurar ambiente de teste:', error);
  }
}

// Função para limpar o ambiente de teste
export async function teardownTestEnvironment() {
  try {
    await clearDatabase();
    await prisma.$disconnect();
  } catch (error) {
    console.log('Erro ao limpar ambiente de teste:', error);
  }
}