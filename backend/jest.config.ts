import type { Config } from '@jest/types';

const config: Config.InitialOptions = {
  // Ambiente de execução dos testes
  testEnvironment: 'node',

  // Diretórios que o Jest deve procurar por testes
  roots: ['<rootDir>/src'],

  // Padrão de arquivos de teste
  testMatch: [
    '**/__tests__/**/*.+(ts|tsx|js)',
    '**/?(*.)+(spec|test).+(ts|tsx|js)'
  ],

  // Transformar arquivos TypeScript
  transform: {
    '^.+\\.(ts|tsx)$': 'ts-jest'
  },

  // Configurações de cobertura de código
  collectCoverage: true,
  coverageDirectory: 'coverage',
  coverageReporters: ['text', 'lcov', 'clover'],
  coveragePathIgnorePatterns: [
    '/node_modules/',
    '/dist/',
    '/coverage/',
    '/__tests__/',
    '/prisma/'
  ],

  // Arquivos a serem ignorados nos testes
  testPathIgnorePatterns: ['/node_modules/', '/dist/'],

  // Configurar variáveis de ambiente para testes
  setupFiles: ['<rootDir>/src/config/test.ts'],

  // Tempo máximo de execução de cada teste
  testTimeout: 10000,

  // Limpar mocks automaticamente entre testes
  clearMocks: true,

  // Mostrar detalhes dos testes
  verbose: true,

  // Configurações do banco de dados de teste
  globalSetup: '<rootDir>/src/config/test.ts',
  globalTeardown: '<rootDir>/src/config/test.ts',

  // Módulos a serem carregados antes dos testes
  moduleNameMapper: {
    '^@/(.*)$': '<rootDir>/src/$1'
  },

  // Extensões de arquivo que o Jest deve considerar
  moduleFileExtensions: ['ts', 'tsx', 'js', 'jsx', 'json', 'node']
};

export default config;