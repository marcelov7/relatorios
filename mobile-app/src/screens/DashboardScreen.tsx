import { useFocusEffect } from '@react-navigation/native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import React, { useCallback, useEffect, useState } from 'react';
import {
  ActivityIndicator,
  RefreshControl,
  ScrollView,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from 'react-native';
import { useAuth } from '../contexts/AuthContext';
import { AppStackParamList } from '../navigation/RootNavigator';
import {
  DashboardStats,
  Relatorio,
  fetchDashboardStats,
  fetchRecentRelatorios,
} from '../services/relatorioService';
import { colors, fontSize, fontWeight, radius, spacing } from '../theme/tokens';

type Props = NativeStackScreenProps<AppStackParamList, 'Dashboard'>;

interface StatCardProps {
  icon: string;
  label: string;
  value: number | string;
  color: string;
}

function StatCard({ icon, label, value, color }: StatCardProps) {
  return (
    <View style={[styles.statCard, { borderLeftColor: color }]}>
      <Text style={styles.statIcon}>{icon}</Text>
      <Text style={[styles.statValue, { color }]}>{value}</Text>
      <Text style={styles.statLabel}>{label}</Text>
    </View>
  );
}

function getStatusInfo(status: string) {
  const s = (status || '').toLowerCase();
  if (s.includes('conclu') || s.includes('resolv')) {
    return { label: 'Concluída', color: colors.success, bg: 'rgba(34,197,94,0.15)' };
  }
  if (s.includes('andamento')) {
    return { label: 'Em Andamento', color: colors.warning, bg: 'rgba(245,158,11,0.15)' };
  }
  if (s.includes('cancel')) {
    return { label: 'Cancelada', color: colors.danger, bg: 'rgba(239,68,68,0.15)' };
  }
  return { label: 'Aberta', color: colors.info, bg: 'rgba(59,130,246,0.15)' };
}

function RecentItem({ item, onPress }: { item: Relatorio; onPress: () => void }) {
  const statusInfo = getStatusInfo(item.status);
  const progresso = item.progresso ?? 0;
  const setorNome = item.setor?.nome || item.sector || 'Geral';
  const autorNome = item.user?.name || item.autor?.name || item.nome_responsavel || 'Sistema';

  return (
    <TouchableOpacity style={styles.recentItem} onPress={onPress} activeOpacity={0.75}>
      <View style={styles.recentTopRow}>
        <Text style={styles.recentTitle} numberOfLines={1}>
          {item.titulo}
        </Text>
        <View style={[styles.statusBadge, { backgroundColor: statusInfo.bg, borderColor: statusInfo.color }]}>
          <Text style={[styles.statusText, { color: statusInfo.color }]}>{statusInfo.label}</Text>
        </View>
      </View>

      {item.activity ? (
        <Text style={styles.recentActivity}>⚡ {item.activity}</Text>
      ) : null}

      <View style={styles.recentMetaRow}>
        <Text style={styles.recentMeta}>🏢 {setorNome}</Text>
        <Text style={styles.recentMeta}>👤 {autorNome}</Text>
      </View>

      {/* Barra de Progresso */}
      <View style={styles.progressContainer}>
        <View style={styles.progressBarBackground}>
          <View
            style={[
              styles.progressBarFill,
              {
                width: `${Math.min(100, Math.max(0, progresso))}%`,
                backgroundColor:
                  progresso >= 100
                    ? colors.success
                    : progresso >= 50
                    ? colors.warning
                    : colors.info,
              },
            ]}
          />
        </View>
        <Text style={styles.progressText}>{progresso}%</Text>
      </View>

      <View style={styles.recentFooter}>
        <Text style={styles.recentDate}>
          📅 {new Date(item.created_at).toLocaleDateString('pt-BR')} às{' '}
          {new Date(item.created_at).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}
        </Text>
        <Text style={styles.viewMoreText}>Ver detalhes →</Text>
      </View>
    </TouchableOpacity>
  );
}

// ---------------------------------------------------------------------------

export default function DashboardScreen({ navigation }: Props) {
  const { user, logout } = useAuth();
  const [stats, setStats] = useState<DashboardStats | null>(null);
  const [recents, setRecents] = useState<Relatorio[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [lastSync, setLastSync] = useState<Date>(new Date());

  const loadData = useCallback(async (isRefresh = false) => {
    try {
      if (isRefresh) setRefreshing(true);
      setError(null);
      const [statsData, recentData] = await Promise.all([
        fetchDashboardStats(),
        fetchRecentRelatorios(6),
      ]);
      setStats(statsData);
      setRecents(recentData);
      setLastSync(new Date());
    } catch (err: any) {
      if (!stats) {
        setError(err?.response?.data?.message ?? 'Erro ao conectar ao servidor Laravel.');
      }
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, [stats]);

  useFocusEffect(
    useCallback(() => {
      loadData();
    }, [loadData])
  );

  useEffect(() => {
    const interval = setInterval(() => {
      loadData();
    }, 8000);
    return () => clearInterval(interval);
  }, [loadData]);

  const greeting = () => {
    const h = new Date().getHours();
    if (h < 12) return 'Bom dia';
    if (h < 18) return 'Boa tarde';
    return 'Boa noite';
  };

  return (
    <View style={styles.root}>
      <ScrollView
        contentContainerStyle={styles.content}
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={() => loadData(true)}
            tintColor={colors.primary}
            colors={[colors.primary]}
          />
        }
      >
        {/* ── Header ──────────────────────────────────────────────── */}
        <View style={styles.header}>
          <View>
            <Text style={styles.greeting}>{greeting()},</Text>
            <Text style={styles.userName}>
              {user?.name?.split(' ')[0] ?? 'Usuário'} 👋
            </Text>
            {user?.setor && <Text style={styles.userSetor}>🏢 {user.setor}</Text>}
          </View>
          <TouchableOpacity onPress={logout} style={styles.logoutBtn} testID="logout-btn">
            <Text style={styles.logoutText}>Sair</Text>
          </TouchableOpacity>
        </View>

        {/* ── Status de Sincronização em Tempo Real ────────────────── */}
        <View style={styles.liveBar}>
          <View style={styles.liveIndicator}>
            <View style={styles.liveDot} />
            <Text style={styles.liveText}>Sincronização em Tempo Real</Text>
          </View>
          <Text style={styles.syncTime}>
            Atualizado: {lastSync.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit', second: '2-digit' })}
          </Text>
        </View>

        {/* ── Conteúdo ─────────────────────────────────────────── */}
        {loading && !stats ? (
          <ActivityIndicator
            color={colors.primary}
            size="large"
            style={{ marginVertical: 40 }}
          />
        ) : error && !stats ? (
          <View style={styles.errorBox}>
            <Text style={styles.errorText}>⚠️ {error}</Text>
            <TouchableOpacity onPress={() => loadData()} style={styles.retryBtn}>
              <Text style={styles.retryText}>Tentar novamente</Text>
            </TouchableOpacity>
          </View>
        ) : stats ? (
          <>
            {/* ── Cards de Estatísticas ──────────────────────────── */}
            <View style={styles.statsGrid}>
              <StatCard
                icon="📄"
                label="Total"
                value={stats.total_relatorios}
                color={colors.info}
              />
              <StatCard
                icon="🔓"
                label="Abertos"
                value={stats.relatorios_abertos}
                color={colors.warning}
              />
              <StatCard
                icon="⚙️"
                label="Em Andamento"
                value={stats.relatorios_em_andamento}
                color={colors.accent}
              />
              <StatCard
                icon="✅"
                label="Concluídos"
                value={stats.relatorios_resolvidos}
                color={colors.success}
              />
              <StatCard
                icon="👤"
                label="Meus"
                value={stats.meus_relatorios}
                color={colors.primaryLight}
              />
              <StatCard
                icon="🚨"
                label="Progresso < 50%"
                value={stats.relatorios_criticos}
                color={colors.danger}
              />
            </View>

            {/* ── Botão para Lista Completa ──────────────────────── */}
            <TouchableOpacity
              style={styles.viewAllBtn}
              onPress={() => navigation.navigate('Relatorios')}
              testID="nav-relatorios"
              activeOpacity={0.8}
            >
              <Text style={styles.viewAllText}>📋 Ver Todos os Relatórios ({stats.total_relatorios}) →</Text>
            </TouchableOpacity>

            {/* ── Relatórios Recentes ───────────────────────────── */}
            <View style={styles.sectionHeader}>
              <Text style={styles.sectionTitle}>Últimos Relatórios Criados</Text>
              <Text style={styles.sectionBadge}>Tempo Real</Text>
            </View>

            {recents.length === 0 ? (
              <Text style={styles.emptyText}>Nenhum relatório cadastrado no Laravel ainda.</Text>
            ) : (
              recents.map((r) => (
                <RecentItem
                  key={r.id}
                  item={r}
                  onPress={() => navigation.navigate('RelatorioDetail', { id: r.id })}
                />
              ))
            )}
          </>
        ) : null}
      </ScrollView>

      {/* ── Floating Action Button (FAB) "+ Novo Relatório" ── */}
      <TouchableOpacity
        style={styles.fabBtn}
        onPress={() => navigation.navigate('RelatorioCreate')}
        activeOpacity={0.85}
      >
        <Text style={styles.fabIcon}>＋</Text>
        <Text style={styles.fabText}>Novo Relatório</Text>
      </TouchableOpacity>
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.background },
  content: { padding: spacing.md, paddingBottom: 100 },

  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: spacing.md,
  },
  greeting: { fontSize: fontSize.sm, color: colors.textSecondary },
  userName: {
    fontSize: fontSize.xl,
    fontWeight: fontWeight.bold,
    color: colors.textPrimary,
  },
  userSetor: { fontSize: fontSize.xs, color: colors.textMuted, marginTop: 2 },
  logoutBtn: {
    backgroundColor: 'rgba(239,68,68,0.15)',
    borderWidth: 1,
    borderColor: colors.danger,
    borderRadius: radius.full,
    paddingHorizontal: spacing.md,
    paddingVertical: spacing.xs,
  },
  logoutText: {
    color: colors.danger,
    fontSize: fontSize.sm,
    fontWeight: fontWeight.semibold,
  },

  liveBar: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    backgroundColor: 'rgba(34, 197, 94, 0.08)',
    borderWidth: 1,
    borderColor: 'rgba(34, 197, 94, 0.25)',
    borderRadius: radius.sm,
    paddingHorizontal: spacing.sm,
    paddingVertical: 6,
    marginBottom: spacing.lg,
  },
  liveIndicator: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  liveDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: colors.success,
  },
  liveText: {
    fontSize: fontSize.xs,
    color: colors.success,
    fontWeight: fontWeight.semibold,
  },
  syncTime: {
    fontSize: fontSize.xs,
    color: colors.textMuted,
  },

  statsGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: spacing.sm,
    marginBottom: spacing.md,
  },
  statCard: {
    flex: 1,
    minWidth: '45%',
    backgroundColor: colors.card,
    borderRadius: radius.md,
    borderWidth: 1,
    borderColor: colors.cardBorder,
    borderLeftWidth: 4,
    padding: spacing.md,
    alignItems: 'center',
  },
  statIcon: { fontSize: 24, marginBottom: spacing.xs },
  statValue: { fontSize: fontSize.xxl, fontWeight: fontWeight.extrabold },
  statLabel: { fontSize: fontSize.xs, color: colors.textSecondary, marginTop: 2 },

  viewAllBtn: {
    backgroundColor: colors.surfaceElevated,
    borderWidth: 1,
    borderColor: colors.primaryLight,
    borderRadius: radius.md,
    padding: spacing.md,
    alignItems: 'center',
    marginBottom: spacing.lg,
  },
  viewAllText: {
    color: colors.primaryLight,
    fontSize: fontSize.md,
    fontWeight: fontWeight.semibold,
  },

  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: spacing.md,
  },
  sectionTitle: {
    fontSize: fontSize.lg,
    fontWeight: fontWeight.bold,
    color: colors.textPrimary,
  },
  sectionBadge: {
    fontSize: fontSize.xs,
    color: colors.success,
    backgroundColor: 'rgba(34, 197, 94, 0.15)',
    paddingHorizontal: spacing.sm,
    paddingVertical: 2,
    borderRadius: radius.full,
    fontWeight: fontWeight.semibold,
  },

  recentItem: {
    backgroundColor: colors.card,
    borderRadius: radius.md,
    borderWidth: 1,
    borderColor: colors.cardBorder,
    padding: spacing.md,
    marginBottom: spacing.sm,
  },
  recentTopRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 4,
  },
  recentTitle: {
    flex: 1,
    fontSize: fontSize.md,
    fontWeight: fontWeight.semibold,
    color: colors.textPrimary,
    marginRight: spacing.sm,
  },
  statusBadge: {
    borderRadius: radius.full,
    borderWidth: 1,
    paddingHorizontal: spacing.sm,
    paddingVertical: 2,
  },
  statusText: {
    fontSize: fontSize.xs,
    fontWeight: fontWeight.semibold,
  },
  recentActivity: {
    fontSize: fontSize.sm,
    color: colors.accentLight,
    fontWeight: fontWeight.medium,
    marginBottom: 4,
  },
  recentMetaRow: {
    flexDirection: 'row',
    gap: spacing.md,
    marginBottom: 6,
  },
  recentMeta: {
    fontSize: fontSize.xs,
    color: colors.textSecondary,
  },

  progressContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: spacing.sm,
    marginVertical: 4,
  },
  progressBarBackground: {
    flex: 1,
    height: 6,
    backgroundColor: colors.surfaceElevated,
    borderRadius: radius.full,
    overflow: 'hidden',
  },
  progressBarFill: {
    height: '100%',
    borderRadius: radius.full,
  },
  progressText: {
    fontSize: fontSize.xs,
    fontWeight: fontWeight.semibold,
    color: colors.textSecondary,
    width: 35,
    textAlign: 'right',
  },

  recentFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginTop: 6,
    paddingTop: 6,
    borderTopWidth: 1,
    borderTopColor: colors.surfaceElevated,
  },
  recentDate: { fontSize: fontSize.xs, color: colors.textMuted },
  viewMoreText: { fontSize: fontSize.xs, color: colors.primaryLight, fontWeight: fontWeight.semibold },

  errorBox: { alignItems: 'center', padding: spacing.xl },
  errorText: { color: colors.danger, fontSize: fontSize.md, marginBottom: spacing.md },
  retryBtn: {
    backgroundColor: colors.primary,
    borderRadius: radius.sm,
    paddingHorizontal: spacing.lg,
    paddingVertical: spacing.sm,
  },
  retryText: { color: '#fff', fontWeight: fontWeight.semibold },
  emptyText: {
    color: colors.textMuted,
    fontSize: fontSize.md,
    textAlign: 'center',
    padding: spacing.lg,
  },

  fabBtn: {
    position: 'absolute',
    bottom: 24,
    right: 20,
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    backgroundColor: colors.primary,
    paddingVertical: 14,
    paddingHorizontal: 20,
    borderRadius: radius.full,
    shadowColor: colors.primary,
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.5,
    shadowRadius: 12,
    elevation: 8,
  },
  fabIcon: { color: '#fff', fontSize: 18, fontWeight: fontWeight.extrabold },
  fabText: { color: '#fff', fontSize: fontSize.sm, fontWeight: fontWeight.bold, letterSpacing: 0.3 },
});
