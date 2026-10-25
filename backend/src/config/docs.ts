import swaggerJsdoc from 'swagger-jsdoc';
import { SERVER } from './env';

// Configuração base do Swagger
const swaggerOptions = {
  definition: {
    openapi: '3.0.0',
    info: {
      title: 'API de Sistema de Relatórios',
      version: '1.0.0',
      description: 'Documentação da API do Sistema de Relatórios',
      contact: {
        name: 'Equipe de Desenvolvimento',
        email: 'dev@sistema.com'
      }
    },
    servers: [
      {
        url: SERVER.API_URL,
        description: `Servidor ${SERVER.NODE_ENV}`
      }
    ],
    components: {
      securitySchemes: {
        bearerAuth: {
          type: 'http',
          scheme: 'bearer',
          bearerFormat: 'JWT'
        }
      }
    },
    security: [
      {
        bearerAuth: []
      }
    ]
  },
  apis: [
    './src/routes/*.ts',
    './src/controllers/*.ts',
    './src/models/*.ts'
  ]
};

// Geração da especificação Swagger
const swaggerSpec = swaggerJsdoc(swaggerOptions);

export { swaggerSpec };