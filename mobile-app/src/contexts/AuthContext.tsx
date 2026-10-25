import React, {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useState,
} from 'react';
import {
  AuthUser,
  LoginPayload,
  fetchCurrentUser,
  loadStoredUser,
  login as loginService,
  logout as logoutService,
} from '../services/authService';

// ---------------------------------------------------------------------------

interface AuthContextValue {
  user: AuthUser | null;
  isLoading: boolean;      // checando token salvo na inicialização
  isLoggingIn: boolean;    // durante POST /login
  error: string | null;
  login: (payload: LoginPayload) => Promise<void>;
  logout: () => Promise<void>;
  clearError: () => void;
}

const AuthContext = createContext<AuthContextValue | undefined>(undefined);

// ---------------------------------------------------------------------------

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [user, setUser] = useState<AuthUser | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [isLoggingIn, setIsLoggingIn] = useState(false);
  const [error, setError] = useState<string | null>(null);

  // Na inicialização, tenta recarregar sessão salva localmente
  useEffect(() => {
    (async () => {
      try {
        const stored = await loadStoredUser();
        if (stored) {
          // valida o token ainda consultando o servidor
          const fresh = await fetchCurrentUser().catch(() => null);
          setUser(fresh ?? stored);
        }
      } catch {
        setUser(null);
      } finally {
        setIsLoading(false);
      }
    })();
  }, []);

  const login = useCallback(async (payload: LoginPayload) => {
    setIsLoggingIn(true);
    setError(null);
    try {
      const loggedUser = await loginService(payload);
      setUser(loggedUser);
    } catch (err: any) {
      const message =
        err?.response?.data?.message ??
        err?.message ??
        'Erro ao fazer login. Verifique suas credenciais.';
      setError(message);
      throw err;
    } finally {
      setIsLoggingIn(false);
    }
  }, []);

  const logout = useCallback(async () => {
    await logoutService();
    setUser(null);
  }, []);

  const clearError = useCallback(() => setError(null), []);

  return (
    <AuthContext.Provider
      value={{ user, isLoading, isLoggingIn, error, login, logout, clearError }}
    >
      {children}
    </AuthContext.Provider>
  );
}

// ---------------------------------------------------------------------------

export function useAuth(): AuthContextValue {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error('useAuth deve ser usado dentro de <AuthProvider>');
  return ctx;
}
