# SGR Mobile — Cliente React Native / Expo

## Estrutura do Projeto

```
mobile-app/
├── App.tsx                        ← Entry point (GestureHandler + AuthProvider + NavigationContainer)
├── index.ts                       ← Registro do Expo
├── app.json                       ← Config Expo (slug, ícones, orientação)
├── package.json                   ← Dependências
├── tsconfig.json                  ← TypeScript (extends expo/tsconfig.base)
└── src/
    ├── config/
    │   └── api.ts                 ← URL base da API (ajuste para IP real)
    ├── contexts/
    │   └── AuthContext.tsx        ← Estado global de autenticação
    ├── navigation/
    │   └── RootNavigator.tsx      ← Roteamento: Auth stack vs. App stack
    ├── screens/
    │   ├── LoginScreen.tsx        ← Tela de login com Sanctum
    │   ├── DashboardScreen.tsx    ← Dashboard com 6 cards de stats
    │   └── RelatoriosScreen.tsx   ← Lista paginada com filtros e pesquisa
    ├── services/
    │   ├── authService.ts         ← login / logout / fetchCurrentUser
    │   └── relatorioService.ts    ← fetchDashboardStats / fetchRelatorios
    └── theme/
        └── tokens.ts              ← Design tokens (cores, espaçamentos, tipografia)
```

## Dependências Instaladas

| Pacote | Versão | Uso |
|--------|--------|-----|
| expo | ~57 | Runtime principal |
| react-native | 0.86 | Framework mobile |
| @react-navigation/native | latest | Navegação |
| @react-navigation/native-stack | latest | Stack navigator |
| react-native-screens | latest | Otimização de telas |
| react-native-safe-area-context | latest | SafeArea |
| react-native-gesture-handler | latest | Gestos |
| react-native-reanimated | latest | Animações |
| expo-secure-store | latest | Token persistido seguro |
| expo-constants | latest | Info do app |
| axios | latest | HTTP client |

## Fluxo de Autenticação (Sanctum)

```
App inicia
  └── AuthProvider: loadStoredUser()
        ├── Token salvo? → fetchCurrentUser() para validar
        │     ├── OK → vai para Dashboard
        │     └── Erro → vai para Login (token expirado)
        └── Sem token → vai para Login

Login (POST /api/auth/login)
  └── Resposta: { success, data: { user, token, token_type } }
        └── Salva token no SecureStore
              └── Navega para Dashboard

Logout (POST /api/auth/logout)
  └── Revoga token no servidor
        └── Apaga SecureStore → volta para Login
```

## Endpoints Utilizados

| Método | Rota | Descrição |
|--------|------|-----------|
| POST | `/api/auth/login` | Login (retorna token Sanctum) |
| POST | `/api/auth/logout` | Logout (revoga token) |
| GET | `/api/auth/user` | Usuário autenticado |
| GET | `/api/dashboard/stats` | Estatísticas do dashboard |
| GET | `/api/dashboard/recent?limit=5` | Relatórios recentes |
| GET | `/api/relatorios?page=1&status=&search=` | Lista paginada |

## Como Rodar

### Pré-requisitos
- Node.js 18+
- Expo Go instalado no celular **ou** Android Studio + emulador

### Desenvolvimento

```bash
cd mobile-app
npm start          # Abre o Expo DevTools
```

Depois escaneie o QR code com o Expo Go (celular físico) ou pressione:
- `a` → emulador Android
- `i` → simulador iOS (macOS)
- `w` → browser (limitado)

### Configuração do IP da API

Edite `src/config/api.ts`:

```ts
// Android emulador (padrão)
export const API_BASE_URL = 'http://10.0.2.2:8000';

// Celular físico na mesma rede
export const API_BASE_URL = 'http://192.168.x.x:8000';

// Produção
export const API_BASE_URL = 'https://seu-dominio.com';
```

### Rodar o servidor Laravel

```bash
cd ..   # pasta raiz do projeto
php artisan serve --port=8000
```

## Funcionalidades Implementadas

- [x] **Login** com Sanctum token (armazenado no SecureStore)
- [x] **Restauração de sessão** na inicialização do app
- [x] **Dashboard** com 6 cards: Total, Abertos, Em Andamento, Resolvidos, Meus, Críticos
- [x] **Relatórios recentes** no dashboard
- [x] **Navegação Dashboard → Relatórios**
- [x] **Lista de relatórios** com paginação infinita
- [x] **Pesquisa** com debounce de 500ms
- [x] **Filtros de status**: Todos / Aberto / Em Andamento / Resolvido
- [x] **Pull-to-refresh** em todas as telas
- [x] **Logout** com revogação do token
- [x] **Tema escuro** profissional

## Próximos Passos Sugeridos

- [ ] Tela de detalhes do relatório
- [ ] Bottom Tab Navigator (Dashboard | Relatórios | Perfil)
- [ ] Notificações push (expo-notifications)
- [ ] Criação de relatório via mobile
- [ ] Upload de fotos (expo-image-picker)
- [ ] Modo offline com cache local
