import { PrismaClient, UserRole, EquipamentoStatus } from '@prisma/client';
import bcrypt from 'bcryptjs';

const prisma = new PrismaClient();

async function main() {
  try {
    console.log('Iniciando seed do banco de dados...');

    // Criar usuário administrador
    const adminPassword = await bcrypt.hash('admin123', 10);
    const admin = await prisma.user.create({
      data: {
        email: 'admin@example.com',
        username: 'admin',
        name: 'Administrador',
        password: adminPassword,
        role: 'ADMIN',
        active: true
      }
    });
    console.log('Usuário administrador criado:', admin.email);

    // Criar alguns usuários de exemplo
    const users = await Promise.all([
      prisma.user.create({
        data: {
          email: 'manager@example.com',
          username: 'manager',
          name: 'Gerente',
          password: await bcrypt.hash('manager123', 10),
          role: 'MANAGER',
          active: true
        }
      }),
      prisma.user.create({
        data: {
          email: 'user@example.com',
          username: 'user',
          name: 'Usuário',
          password: await bcrypt.hash('user123', 10),
          role: 'USER',
          active: true
        }
      })
    ]);
    console.log('Usuários de exemplo criados:', users.map(u => u.email));

    // Criar alguns setores
    const setores = await Promise.all([
      prisma.setor.create({
        data: {
          nome: 'Manutenção',
          descricao: 'Setor responsável pela manutenção dos equipamentos',
          ativo: true
        }
      }),
      prisma.setor.create({
        data: {
          nome: 'Produção',
          descricao: 'Setor responsável pela produção',
          ativo: true
        }
      }),
      prisma.setor.create({
        data: {
          nome: 'Qualidade',
          descricao: 'Setor responsável pelo controle de qualidade',
          ativo: true
        }
      })
    ]);
    console.log('Setores criados:', setores.map(s => s.nome));

    // Criar alguns equipamentos
    const equipamentos = await Promise.all([
      prisma.equipamento.create({
        data: {
          nome: 'Torno CNC',
          modelo: 'TC-1000',
          numeroSerie: 'TC1000-001',
          localizacao: 'Galpão 1',
          status: 'ATIVO',
          setorId: setores[1].id // Setor de Produção
        }
      }),
      prisma.equipamento.create({
        data: {
          nome: 'Fresadora',
          modelo: 'FR-500',
          numeroSerie: 'FR500-001',
          localizacao: 'Galpão 1',
          status: 'ATIVO',
          setorId: setores[1].id // Setor de Produção
        }
      }),
      prisma.equipamento.create({
        data: {
          nome: 'Máquina de Solda',
          modelo: 'MS-200',
          numeroSerie: 'MS200-001',
          localizacao: 'Galpão 2',
          status: 'MANUTENCAO',
          setorId: setores[0].id // Setor de Manutenção
        }
      })
    ]);
    console.log('Equipamentos criados:', equipamentos.map(e => e.nome));

    console.log('Seed concluído com sucesso!');
  } catch (error) {
    console.error('Erro durante o seed:', error);
    throw error;
  } finally {
    await prisma.$disconnect();
  }
}

main()
  .catch((error) => {
    console.error(error);
    process.exit(1);
  });