import axios from 'axios';
import * as SecureStore from 'expo-secure-store';
import { API_BASE_URL, API_TIMEOUT } from '../config/api';

const TOKEN_KEY = 'sanctum_token';
const USER_KEY = 'auth_user';

// Instância axios configurada para o Laravel/Sanctum
const api = axios.create({
  baseURL: `${API_BASE_URL}/api`,
  timeout: API_TIMEOUT,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
});

// Interceptor: injeta o token em toda requisição autenticada
api.interceptors.request.use(async (config) => {
  const token = await SecureStore.getItemAsync(TOKEN_KEY);
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// ---------------------------------------------------------------------------

export interface LoginPayload {
  email: string;
  password: string;
}

export interface AuthUser {
  id: number;
  name: string;
  email: string;
  role?: string;
  setor?: string;
  cargo?: string;
  empresa_id?: number;
}

// Formato real retornado pelo AuthController@login:
// { success: true, data: { user: {...}, token: '...', token_type: 'Bearer' } }
interface LoginApiResponse {
  success: boolean;
  message: string;
  data: {
    user: AuthUser;
    token: string;
    token_type: string;
  };
}

// Formato real retornado pelo AuthController@user:
// { success: true, data: { user: {...} } }
interface UserApiResponse {
  success: boolean;
  data: { user: AuthUser };
}

/**
 * Autentica via POST /api/auth/login e persiste o token no SecureStore.
 */
export async function login(payload: LoginPayload): Promise<AuthUser> {
  const { data } = await api.post<LoginApiResponse>('/auth/login', payload);
  if (!data.success) {
    throw new Error(data.message || 'Credenciais inválidas');
  }
  await SecureStore.setItemAsync(TOKEN_KEY, data.data.token);
  await SecureStore.setItemAsync(USER_KEY, JSON.stringify(data.data.user));
  return data.data.user;
}

/**
 * Busca o usuário autenticado via GET /api/auth/user.
 */
export async function fetchCurrentUser(): Promise<AuthUser> {
  const { data } = await api.get<UserApiResponse>('/auth/user');
  const user = data.data?.user ?? (data as any);
  await SecureStore.setItemAsync(USER_KEY, JSON.stringify(user));
  return user;
}

/**
 * Faz logout: revoga o token no servidor e apaga o armazenamento local.
 */
export async function logout(): Promise<void> {
  try {
    await api.post('/auth/logout');
  } catch (_) {
    // ignora erros de rede ao fazer logout
  } finally {
    await SecureStore.deleteItemAsync(TOKEN_KEY);
    await SecureStore.deleteItemAsync(USER_KEY);
  }
}

/**
 * Carrega o usuário salvo localmente sem chamar a API.
 */
export async function loadStoredUser(): Promise<AuthUser | null> {
  const raw = await SecureStore.getItemAsync(USER_KEY);
  const token = await SecureStore.getItemAsync(TOKEN_KEY);
  if (!raw || !token) return null;
  return JSON.parse(raw) as AuthUser;
}

export { api };
