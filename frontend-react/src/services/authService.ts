import axios from 'axios';

const API_URL = 'http://localhost:3001';

export interface LoginCredentials {
  login: string;
  senha: string;  // Alterado de "password" para "senha" para corresponder à API
}

export interface LoginResponse {
  success: boolean;
  token?: string;
  message?: string;
}

export const login = async (credentials: LoginCredentials): Promise<LoginResponse> => {
  try {
    const response = await axios.post(`${API_URL}/api/mock/login`, credentials);
    console.log('Resposta da API:', response.data); // Log para debug
    
    // Baseado na resposta que vimos no teste: { success: True, data: @{token=mock-jwt-token-123456; user=} }
    if (response.data.success) {
      // A resposta tem estrutura { success: true, data: { token: string, user: object } }
      const token = response.data.data?.token;
      if (token) {
        localStorage.setItem('token', token);
        return { success: true, token };
      }
    }
    return { success: false, message: response.data.error || 'Falha na autenticação' };
  } catch (error: any) {
    console.error('Erro ao fazer login:', error);
    // Captura mensagens de erro da API
    const errorMessage = error.response?.data?.error || 'Erro ao conectar com o servidor';
    return { success: false, message: errorMessage };
  }
};

export const logout = (): void => {
  localStorage.removeItem('token');
};

export const isAuthenticated = (): boolean => {
  return !!localStorage.getItem('token');
};