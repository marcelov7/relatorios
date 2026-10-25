import { useFocusEffect } from '@react-navigation/native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import React, { useCallback, useEffect, useState } from 'react';
import {
  ActivityIndicator,
  FlatList,
  RefreshControl,
  StyleSheet,
  Text,
  TextInput,
  TouchableOpacity,
  View,
} from 'react-native';
import { AppStackParamList } from '../navigation/RootNavigator';
import { Relatorio, fetchRelatorios } from '../services/relatorioService';
import { colors, fontSize, fontWeight, radius, spacing } from '../theme/tokens';

type Props = NativeStackScreenProps<AppStackParamList, 'Relatorios'>;

const STATUS_OPTIONS = ['todos', 'aberto', 'em_andamento', 'resolvido'] as const;
type StatusFilter = (typeof STATUS_OPTIONS)[number];

const STATUS_LABELS: Record<StatusFilter, string> = {
  todos: 'Todos',
  aberto: 'Abertos',
  em_andamento: 'Em Andamento',
  resolvido: 'Concluídos',
};

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

function RelatorioCard({ item, onPress }: { item: Relatorio; onPress: () => void }) {
  const statusInfo = getStatusInfo(item.status);
  const progresso = item.progresso ?? 0;
  const setorNome = item.setor?.nome || item.sector || 'Geral';
  const autorNome = item.user?.name || item.autor?.name || item.nome_responsavel || 'Sistema';

  return (
    <TouchableOpacity style={styles.card} onPress={onPress} activeOpacity={0.75}>
      <View style={styles.cardHeader}>
        <Text style={styles.cardTitle} numberOfLines={2}>
          {item.titulo}
        </Text>
        <View style={[styles.badge, { backgroundColor: statusInfo.bg, borderColor: statusInfo.color }]}>
          <Text style={[styles.badgeText, { color: statusInfo.color }]}>
            {statusInfo.label}
          </Text>
        </View>
      </View>

      {item.activity ? (
        <Text style={styles.activityText}>⚡ {item.activity}</Text>
      ) : null}

      <View style={styles.metaRow}>
        <Text style={styles.metaItem}>🏢 {setorNome}</Text>
        <Text style={styles.metaItem}>👤 {autorNome}</Text>
      </View>

      {/* Barra de Progresso do Relatório */}
      <View style={styles.progressRow}>
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
        <Text style={styles.progressLabel}>{progresso}%</Text>
      </View>

      {item.detalhes ? (
        <Text style={styles.detalhesText} numberOfLines={2}>
          {item.detalhes}
        </Text>
      ) : null}

      <View style={styles.footerRow}>
        <Text style={styles.date}>
          📅 {new Date(item.created_at).toLocaleDateString('pt-BR')} às{' '}
          {new Date(item.created_at).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}
        </Text>
        <Text style={styles.detailsActionText}>Ver detalhes →</Text>
      </View>
    </TouchableOpacity>
  );
}

// ---------------------------------------------------------------------------

export default function RelatoriosScreen({ navigation }: Props) {
  const [relatorios, setRelatorios] = useState<Relatorio[]>([]);
  const [loading, setLoading] = useState(true);
  const [loadingMore, setLoadingMore] = useState(false);
  const [refreshing, setRefreshing] = useState(false);
  const [page, setPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);
  const [totalCount, setTotalCount] = useState(0);
  const [error, setError] = useState<string | null>(null);
  const [search, setSearch] = useState('');
  const [status, setStatus] = useState<StatusFilter>('todos');
  const [searchDebounce, setSearchDebounce] = useState('');

  useEffect(() => {
    const timer = setTimeout(() => setSearchDebounce(search), 500);
    return () => clearTimeout(timer);
  }, [search]);

  const load = useCallback(
    async (reset = false, isRefresh = false) => {
      const targetPage = reset ? 1 : page;
      if (!reset && targetPage > lastPage) return;

      try {
        if (reset) {
          if (isRefresh) setRefreshing(true);
          else setLoading(true);
        } else {
          setLoadingMore(true);
        }
        setError(null);

        const result = await fetchRelatorios({
          page: targetPage,
          status: status === 'todos' ? undefined : status,
          search: searchDebounce || undefined,
        });

        setRelatorios((prev) => (reset ? result.data : [...prev, ...result.data]));
        setLastPage(result.last_page);
        setTotalCount(result.total);
        setPage(targetPage + 1);
      } catch (err: any) {
        setError(err?.response?.data?.message ?? 'Erro ao carregar relatórios do Laravel.');
      } finally {
        setLoading(false);
        setLoadingMore(false);
        setRefreshing(false);
      }
    },
    [page, lastPage, status, searchDebounce]
  );

  useFocusEffect(
    useCallback(() => {
      setPage(1);
      load(true);
    }, [status, searchDebounce])
  );

  return (
    <View style={styles.root}>
      {/* ── Barra de Pesquisa ────────────────────────────────── */}
      <View style={styles.searchBar}>
        <Text style={styles.searchIcon}>🔍</Text>
        <TextInput
          style={styles.searchInput}
          placeholder="Buscar por título, atividade, setor ou detalhes..."
          placeholderTextColor={colors.textMuted}
          value={search}
          onChangeText={setSearch}
          returnKeyType="search"
          testID="search-input"
        />
        {search.length > 0 && (
          <TouchableOpacity onPress={() => setSearch('')}>
            <Text style={styles.clearIcon}>✕</Text>
          </TouchableOpacity>
        )}
      </View>

      {/* ── Filtros de Status ────────────────────────────────── */}
      <View style={styles.filters}>
        {STATUS_OPTIONS.map((f) => (
          <TouchableOpacity
            key={f}
            style={[styles.filterBtn, status === f && styles.filterBtnActive]}
            onPress={() => setStatus(f)}
            testID={`filter-${f}`}
          >
            <Text style={[styles.filterText, status === f && styles.filterTextActive]}>
              {STATUS_LABELS[f]}
            </Text>
          </TouchableOpacity>
        ))}
      </View>

      {/* ── Totalizador ──────────────────────────────────────── */}
      <View style={styles.countRow}>
        <Text style={styles.countText}>
          Exibindo {relatorios.length} de {totalCount} relatórios encontrados
        </Text>
      </View>

      {/* ── Lista de Relatórios ───────────────────────────────── */}
      {loading && relatorios.length === 0 ? (
        <ActivityIndicator color={colors.primary} size="large" style={{ marginTop: 60 }} />
      ) : error && relatorios.length === 0 ? (
        <View style={styles.errorBox}>
          <Text style={styles.errorText}>⚠️ {error}</Text>
          <TouchableOpacity onPress={() => load(true)} style={styles.retryBtn}>
            <Text style={styles.retryText}>Tentar novamente</Text>
          </TouchableOpacity>
        </View>
      ) : (
        <FlatList
          data={relatorios}
          keyExtractor={(r) => String(r.id)}
          renderItem={({ item }) => (
            <RelatorioCard
              item={item}
              onPress={() => navigation.navigate('RelatorioDetail', { id: item.id })}
            />
          )}
          contentContainerStyle={styles.list}
          onEndReached={() => load(false)}
          onEndReachedThreshold={0.4}
          refreshControl={
            <RefreshControl
              refreshing={refreshing}
              onRefresh={() => load(true, true)}
              tintColor={colors.primary}
              colors={[colors.primary]}
            />
          }
          ListFooterComponent={
            loadingMore ? (
              <ActivityIndicator color={colors.primary} style={{ margin: spacing.lg }} />
            ) : null
          }
          ListEmptyComponent={
            <View style={styles.emptyBox}>
              <Text style={styles.emptyIcon}>📂</Text>
              <Text style={styles.emptyText}>Nenhum relatório encontrado para este filtro.</Text>
            </View>
          }
        />
      )}

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

  searchBar: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: colors.inputBg,
    borderWidth: 1,
    borderColor: colors.inputBorder,
    borderRadius: radius.sm,
    margin: spacing.md,
    marginBottom: spacing.xs,
    paddingHorizontal: spacing.sm,
    height: 46,
  },
  searchIcon: { fontSize: 16, marginRight: spacing.sm },
  searchInput: { flex: 1, color: colors.textPrimary, fontSize: fontSize.md },
  clearIcon: { color: colors.textMuted, fontSize: 16, paddingLeft: spacing.sm },

  filters: {
    flexDirection: 'row',
    paddingHorizontal: spacing.md,
    gap: spacing.xs,
    marginVertical: spacing.sm,
  },
  filterBtn: {
    paddingHorizontal: spacing.md,
    paddingVertical: spacing.xs,
    borderRadius: radius.full,
    backgroundColor: colors.surface,
    borderWidth: 1,
    borderColor: colors.cardBorder,
  },
  filterBtnActive: {
    backgroundColor: colors.primary,
    borderColor: colors.primary,
  },
  filterText: { fontSize: fontSize.xs, color: colors.textSecondary, fontWeight: fontWeight.medium },
  filterTextActive: { color: '#fff' },

  countRow: {
    paddingHorizontal: spacing.md,
    marginBottom: spacing.xs,
  },
  countText: {
    fontSize: fontSize.xs,
    color: colors.textMuted,
  },

  list: { padding: spacing.md, paddingTop: spacing.xs, paddingBottom: 100 },

  card: {
    backgroundColor: colors.card,
    borderRadius: radius.md,
    borderWidth: 1,
    borderColor: colors.cardBorder,
    padding: spacing.md,
    marginBottom: spacing.sm,
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 4,
  },
  cardTitle: {
    flex: 1,
    fontSize: fontSize.md,
    fontWeight: fontWeight.semibold,
    color: colors.textPrimary,
    marginRight: spacing.sm,
  },
  badge: {
    borderRadius: radius.full,
    borderWidth: 1,
    paddingHorizontal: spacing.sm,
    paddingVertical: 3,
  },
  badgeText: { fontSize: fontSize.xs, fontWeight: fontWeight.semibold },

  activityText: {
    fontSize: fontSize.sm,
    color: colors.accentLight,
    fontWeight: fontWeight.medium,
    marginBottom: 4,
  },

  metaRow: {
    flexDirection: 'row',
    gap: spacing.md,
    marginBottom: 6,
  },
  metaItem: {
    fontSize: fontSize.xs,
    color: colors.textSecondary,
  },

  progressRow: {
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
  progressLabel: {
    fontSize: fontSize.xs,
    fontWeight: fontWeight.semibold,
    color: colors.textSecondary,
    width: 35,
    textAlign: 'right',
  },

  detalhesText: {
    fontSize: fontSize.xs,
    color: colors.textMuted,
    marginTop: 4,
    lineHeight: 16,
  },

  footerRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginTop: spacing.sm,
    paddingTop: spacing.xs,
    borderTopWidth: 1,
    borderTopColor: colors.cardBorder,
  },
  date: { fontSize: fontSize.xs, color: colors.textMuted },
  detailsActionText: { fontSize: fontSize.xs, color: colors.primaryLight, fontWeight: fontWeight.semibold },

  errorBox: { alignItems: 'center', padding: spacing.xxl },
  errorText: { color: colors.danger, fontSize: fontSize.md, marginBottom: spacing.md },
  retryBtn: {
    backgroundColor: colors.primary,
    borderRadius: radius.sm,
    paddingHorizontal: spacing.lg,
    paddingVertical: spacing.sm,
  },
  retryText: { color: '#fff', fontWeight: fontWeight.semibold },

  emptyBox: { alignItems: 'center', paddingTop: 60 },
  emptyIcon: { fontSize: 48, marginBottom: spacing.md },
  emptyText: { fontSize: fontSize.md, color: colors.textMuted },

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
