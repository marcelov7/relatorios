import { createNativeStackNavigator } from '@react-navigation/native-stack';
import React from 'react';
import { useAuth } from '../contexts/AuthContext';
import DashboardScreen from '../screens/DashboardScreen';
import LoginScreen from '../screens/LoginScreen';
import RelatorioCreateScreen from '../screens/RelatorioCreateScreen';
import RelatorioDetailScreen from '../screens/RelatorioDetailScreen';
import RelatorioEditScreen from '../screens/RelatorioEditScreen';
import RelatoriosScreen from '../screens/RelatoriosScreen';
import { colors, fontWeight } from '../theme/tokens';

export type AppStackParamList = {
  Dashboard: undefined;
  Relatorios: undefined;
  RelatorioCreate: undefined;
  RelatorioDetail: { id: number };
  RelatorioEdit: { id: number };
};

export type AuthStackParamList = {
  Login: undefined;
};

const AuthStack = createNativeStackNavigator<AuthStackParamList>();
const AppStack = createNativeStackNavigator<AppStackParamList>();

function AuthNavigator() {
  return (
    <AuthStack.Navigator screenOptions={{ headerShown: false }}>
      <AuthStack.Screen name="Login" component={LoginScreen} />
    </AuthStack.Navigator>
  );
}

function AppNavigator() {
  return (
    <AppStack.Navigator
      screenOptions={{
        headerStyle: { backgroundColor: colors.surface },
        headerTintColor: colors.textPrimary,
        headerTitleStyle: { fontWeight: fontWeight.bold },
        contentStyle: { backgroundColor: colors.background },
      }}
    >
      <AppStack.Screen
        name="Dashboard"
        component={DashboardScreen}
        options={{ title: 'Painel Principal' }}
      />
      <AppStack.Screen
        name="Relatorios"
        component={RelatoriosScreen}
        options={{ title: 'Relatórios Operacionais' }}
      />
      <AppStack.Screen
        name="RelatorioCreate"
        component={RelatorioCreateScreen}
        options={{ title: 'Novo Relatório' }}
      />
      <AppStack.Screen
        name="RelatorioDetail"
        component={RelatorioDetailScreen}
        options={{ title: 'Detalhes do Relatório' }}
      />
      <AppStack.Screen
        name="RelatorioEdit"
        component={RelatorioEditScreen}
        options={{ title: 'Evoluir Relatório' }}
      />
    </AppStack.Navigator>
  );
}

export default function RootNavigator() {
  const { user } = useAuth();
  return user ? <AppNavigator /> : <AuthNavigator />;
}
