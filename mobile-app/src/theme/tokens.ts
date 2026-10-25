// Design tokens centralizados para o tema do app
export const colors = {
  // Primária — azul escuro profissional
  primary: '#1B4F8A',
  primaryLight: '#2D6FC4',
  primaryDark: '#0E305A',

  // Secundária — laranja para alertas/ações
  accent: '#F4820A',
  accentLight: '#FFA033',

  // Superfícies
  background: '#0F1923',
  surface: '#172232',
  surfaceElevated: '#1E2E42',
  card: '#1A2B3E',
  cardBorder: '#253650',

  // Texto
  textPrimary: '#FFFFFF',
  textSecondary: '#94A3B8',
  textMuted: '#546278',

  // Status
  success: '#22C55E',
  warning: '#F59E0B',
  danger: '#EF4444',
  info: '#3B82F6',

  // Input
  inputBg: '#1E2E42',
  inputBorder: '#2D4463',
  inputFocus: '#1B4F8A',

  // Overlay
  overlay: 'rgba(0,0,0,0.6)',
};

export const spacing = {
  xs: 4,
  sm: 8,
  md: 16,
  lg: 24,
  xl: 32,
  xxl: 48,
};

export const radius = {
  sm: 8,
  md: 12,
  lg: 16,
  xl: 24,
  full: 999,
};

export const fontSize = {
  xs: 11,
  sm: 13,
  md: 15,
  lg: 18,
  xl: 22,
  xxl: 28,
  xxxl: 36,
};

export const fontWeight = {
  regular: '400' as const,
  medium: '500' as const,
  semibold: '600' as const,
  bold: '700' as const,
  extrabold: '800' as const,
};
