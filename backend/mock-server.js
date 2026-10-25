const express = require('express');
const cors = require('cors');

const app = express();
const PORT = 3001;

// Middlewares
app.use(express.json());
app.use(cors({
  origin: ['http://localhost:3006', 'http://localhost:3002', 'http://localhost:3007'],
  credentials: true,
  methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
  allowedHeaders: ['Content-Type', 'Authorization']
}));

// Rota de mock para login
app.post('/api/mock/login', (req, res) => {
  const { email, login, senha } = req.body;
  
  // Aceita tanto 'email' quanto 'login' como campo de usuário
  const userIdentifier = email || login;
  
  console.log('Tentativa de login:', { userIdentifier, senha });
  
  if (!userIdentifier || !senha) {
    return res.status(400).json({ 
      success: false, 
      error: !userIdentifier ? 'Email ou usuário é obrigatório' : 'Senha é obrigatória' 
    });
  }
  
  // Credenciais de teste - aceita múltiplas opções
  const validCredentials = [
    { user: 'admin@example.com', pass: '123456' },
    { user: 'admin@teste.com', pass: '123456' },
    { user: 'admin', pass: '123456' },
    { user: 'teste', pass: '123456' }
  ];
  
  const isValidLogin = validCredentials.some(cred => 
    cred.user === userIdentifier && cred.pass === senha
  );
  
  if (isValidLogin) {
    return res.json({
      success: true,
      data: {
        token: 'mock-jwt-token-123456',
        user: {
          id: 1,
          name: 'Administrador',
          email: userIdentifier.includes('@') ? userIdentifier : 'admin@teste.com',
          role: 'ADMIN'
        }
      }
    });
  }
  
  return res.status(401).json({ 
    success: false, 
    error: 'Credenciais inválidas' 
  });
});

// Health check
app.get('/health', (req, res) => {
  res.status(200).json({
    status: 'OK',
    message: 'Servidor mock funcionando',
    timestamp: new Date().toISOString()
  });
});

// Iniciar servidor
app.listen(PORT, () => {
  console.log(`🚀 Servidor mock rodando na porta ${PORT}`);
  console.log(`🔑 Credenciais de teste: admin@example.com / 123456`);
  console.log(`🌍 URL de login: http://localhost:${PORT}/api/mock/login`);
});