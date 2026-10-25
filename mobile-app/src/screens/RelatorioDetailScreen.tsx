import { useFocusEffect } from '@react-navigation/native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import * as FileSystem from 'expo-file-system';
import * as Sharing from 'expo-sharing';
import React, { useCallback, useState } from 'react';
import {
  ActivityIndicator,
  Alert,
  Image,
  Linking,
  Modal,
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
  Relatorio,
  deleteRelatorio,
  fetchRelatorio,
  getRelatorioPdfUrl,
} from '../services/relatorioService';
import { colors, fontSize, fontWeight, radius, spacing } from '../theme/tokens';

type Props = NativeStackScreenProps<AppStackParamList, 'RelatorioDetail'>;

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

export default function RelatorioDetailScreen({ route, navigation }: Props) {
  const { id } = route.params;
  const { user } = useAuth();

  const [relatorio, setRelatorio] = useState<Relatorio | null>(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [downloadingPdf, setDownloadingPdf] = useState(false);
  const [selectedPhoto, setSelectedPhoto] = useState<string | null>(null);

  const loadData = useCallback(async (isRefresh = false) => {
    try {
      if (isRefresh) setRefreshing(true);
      else setLoading(true);
      const data = await fetchRelatorio(id);
      setRelatorio(data);
    } catch (err: any) {
      Alert.alert('Erro', err?.response?.data?.message || 'Não foi possível carregar o relatório.');
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, [id]);

  useFocusEffect(
    useCallback(() => {
      loadData();
    }, [loadData])
  );

  const handleDownloadPdf = async () => {
    const pdfUrl = getRelatorioPdfUrl(id);
    try {
      setDownloadingPdf(true);
      await Linking.openURL(pdfUrl);
    } catch (err) {
      Alert.alert('Erro', 'Não foi possível abrir o PDF no navegador.');
    } finally {
      setDownloadingPdf(false);
    }
  };

  const handleDelete = () => {
    Alert.alert(
      'Confirmar Exclusão',
      'Tem certeza que deseja excluir este relatório? Esta ação não pode ser desfeita.',
      [
        { text: 'Cancelar', style: 'cancel' },
        {
          text: 'Excluir',
          style: 'destructive',
          onPress: async () => {
            try {
              await deleteRelatorio(id);
              Alert.alert('Excluído', 'Relatório excluído com sucesso.');
              navigation.goBack();
            } catch (err: any) {
              Alert.alert('Erro', err?.response?.data?.message || 'Erro ao excluir relatório.');
            }
          },
        },
      ]
    );
  };

  if (loading && !relatorio) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator color={colors.primary} size="large" />
      </View>
    );
  }

  if (!relatorio) {
    return (
      <View style={styles.loadingContainer}>
        <Text style={styles.errorText}>Relatório não encontrado.</Text>
      </View>
    );
  }

  const statusInfo = getStatusInfo(relatorio.status);
  const progresso = relatorio.progresso ?? 0;
  const setorNome = relatorio.setor?.nome || relatorio.sector || 'Geral';
  const autorNome = relatorio.user?.name || relatorio.autor?.name || relatorio.nome_responsavel || 'Sistema';
  const fotos = relatorio.imagens_formatadas || [];

  return (
    <View style={styles.root}>
      <ScrollView
        contentContainerStyle={styles.content}
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={() => loadData(true)}
            tintColor={colors.primary}
          />
        }
      >
        {/* Card Principal de Cabeçalho */}
        <View style={styles.mainCard}>
          <View style={styles.headerTop}>
            <Text style={styles.reportId}>Relatório #{relatorio.id}</Text>
            <View style={[styles.statusBadge, { backgroundColor: statusInfo.bg, borderColor: statusInfo.color }]}>
              <Text style={[styles.statusText, { color: statusInfo.color }]}>{statusInfo.label}</Text>
            </View>
          </View>

          <Text style={styles.title}>{relatorio.titulo}</Text>

          {relatorio.activity ? (
            <Text style={styles.activityText}>⚡ {relatorio.activity}</Text>
          ) : null}

          {/* Barra de Progresso */}
          <View style={styles.progressBox}>
            <View style={styles.progressHeader}>
              <Text style={styles.progressTitle}>Progresso da Manutenção</Text>
              <Text style={styles.progressValue}>{progresso}%</Text>
            </View>
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
          </View>
        </View>

        {/* Metadados Operacionais */}
        <View style={styles.sectionCard}>
          <Text style={styles.sectionHeading}>📋 Informações Operacionais</Text>
          <View style={styles.metaGrid}>
            <View style={styles.metaItem}>
              <Text style={styles.metaLabel}>Setor</Text>
              <Text style={styles.metaValue}>🏢 {setorNome}</Text>
            </View>
            <View style={styles.metaItem}>
              <Text style={styles.metaLabel}>Responsável</Text>
              <Text style={styles.metaValue}>👤 {autorNome}</Text>
            </View>
            <View style={styles.metaItem}>
              <Text style={styles.metaLabel}>Data de Criação</Text>
              <Text style={styles.metaValue}>
                📅 {new Date(relatorio.created_at).toLocaleDateString('pt-BR')} às{' '}
                {new Date(relatorio.created_at).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}
              </Text>
            </View>
          </View>
        </View>

        {/* Detalhes da Execução */}
        <View style={styles.sectionCard}>
          <Text style={styles.sectionHeading}>📝 Descrição / Detalhes</Text>
          <Text style={styles.detailsBody}>{relatorio.detalhes || 'Nenhuma descrição informada.'}</Text>
        </View>

        {/* Equipamentos Vinculados */}
        {relatorio.equipamentos && relatorio.equipamentos.length > 0 && (
          <View style={styles.sectionCard}>
            <Text style={styles.sectionHeading}>
              ⚙️ Equipamentos Vinculados ({relatorio.equipamentos.length})
            </Text>
            {relatorio.equipamentos.map((eq) => (
              <View key={eq.id} style={styles.equipCard}>
                <View style={styles.equipTagBadge}>
                  <Text style={styles.equipTagText}>{eq.equipment_tag || 'TAG'}</Text>
                </View>
                <View style={styles.equipInfo}>
                  <Text style={styles.equipName}>{eq.nome}</Text>
                  <Text style={styles.equipSub}>
                    {eq.tipo ? `Tipo: ${eq.tipo}` : ''} {eq.status ? `• Status: ${eq.status}` : ''}
                  </Text>
                </View>
              </View>
            ))}
          </View>
        )}

        {/* Galeria de Fotos e Evidências */}
        {fotos.length > 0 && (
          <View style={styles.sectionCard}>
            <Text style={styles.sectionHeading}>📷 Fotos e Evidências ({fotos.length})</Text>
            <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.photosScroll}>
              {fotos.map((img) => (
                <TouchableOpacity
                  key={img.id}
                  style={styles.photoWrapper}
                  onPress={() => setSelectedPhoto(img.url || img.medium_url || img.thumb_url || null)}
                  activeOpacity={0.8}
                >
                  <Image source={{ uri: img.thumb_url || img.url }} style={styles.photoThumb} />
                </TouchableOpacity>
              ))}
            </ScrollView>
          </View>
        )}
      </ScrollView>

      {/* Barra de Ações Fixa no Rodapé */}
      <View style={styles.footerBar}>
        <TouchableOpacity
          style={styles.editBtn}
          onPress={() => navigation.navigate('RelatorioEdit', { id: relatorio.id })}
          activeOpacity={0.85}
        >
          <Text style={styles.editBtnText}>✏️ Evoluir / Editar</Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={styles.pdfBtn}
          onPress={handleDownloadPdf}
          disabled={downloadingPdf}
          activeOpacity={0.85}
        >
          {downloadingPdf ? (
            <ActivityIndicator color={colors.primaryLight} size="small" />
          ) : (
            <Text style={styles.pdfBtnText}>📄 PDF</Text>
          )}
        </TouchableOpacity>

        <TouchableOpacity style={styles.deleteBtn} onPress={handleDelete} activeOpacity={0.85}>
          <Text style={styles.deleteBtnText}>🗑️</Text>
        </TouchableOpacity>
      </View>

      {/* Modal Lightbox de Foto em Tela Cheia */}
      <Modal visible={!!selectedPhoto} transparent animationType="fade" onRequestClose={() => setSelectedPhoto(null)}>
        <View style={styles.modalOverlay}>
          <TouchableOpacity style={styles.modalCloseBtn} onPress={() => setSelectedPhoto(null)}>
            <Text style={styles.modalCloseText}>✕ Fechar</Text>
          </TouchableOpacity>
          {selectedPhoto && (
            <Image source={{ uri: selectedPhoto }} style={styles.fullImage} resizeMode="contain" />
          )}
        </View>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.background },
  content: { padding: spacing.md, paddingBottom: 100 },
  loadingContainer: { flex: 1, alignItems: 'center', justifyContent: 'center', backgroundColor: colors.background },
  errorText: { color: colors.danger, fontSize: fontSize.md },

  mainCard: {
    backgroundColor: colors.card,
    borderRadius: radius.md,
    borderWidth: 1,
    borderColor: colors.cardBorder,
    padding: spacing.md,
    marginBottom: spacing.sm,
  },
  headerTop: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 6,
  },
  reportId: {
    fontSize: fontSize.xs,
    color: colors.textMuted,
    fontWeight: fontWeight.bold,
  },
  statusBadge: {
    borderRadius: radius.full,
    borderWidth: 1,
    paddingHorizontal: spacing.sm,
    paddingVertical: 3,
  },
  statusText: { fontSize: fontSize.xs, fontWeight: fontWeight.semibold },

  title: {
    fontSize: fontSize.xl,
    fontWeight: fontWeight.extrabold,
    color: colors.textPrimary,
    marginBottom: 4,
  },
  activityText: {
    fontSize: fontSize.sm,
    color: colors.accentLight,
    fontWeight: fontWeight.semibold,
    marginBottom: spacing.sm,
  },

  progressBox: {
    backgroundColor: colors.surfaceElevated,
    borderRadius: radius.sm,
    padding: spacing.sm,
    marginTop: spacing.xs,
  },
  progressHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 6,
  },
  progressTitle: {
    fontSize: fontSize.xs,
    color: colors.textSecondary,
    fontWeight: fontWeight.medium,
  },
  progressValue: {
    fontSize: fontSize.sm,
    fontWeight: fontWeight.extrabold,
    color: colors.textPrimary,
  },
  progressBarBackground: {
    height: 8,
    backgroundColor: colors.background,
    borderRadius: radius.full,
    overflow: 'hidden',
  },
  progressBarFill: {
    height: '100%',
    borderRadius: radius.full,
  },

  sectionCard: {
    backgroundColor: colors.card,
    borderRadius: radius.md,
    borderWidth: 1,
    borderColor: colors.cardBorder,
    padding: spacing.md,
    marginBottom: spacing.sm,
  },
  sectionHeading: {
    fontSize: fontSize.md,
    fontWeight: fontWeight.bold,
    color: colors.textPrimary,
    marginBottom: spacing.sm,
  },

  metaGrid: {
    gap: 8,
  },
  metaItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 4,
    borderBottomWidth: 1,
    borderBottomColor: colors.surfaceElevated,
  },
  metaLabel: {
    fontSize: fontSize.xs,
    color: colors.textMuted,
  },
  metaValue: {
    fontSize: fontSize.xs,
    color: colors.textPrimary,
    fontWeight: fontWeight.medium,
  },

  detailsBody: {
    fontSize: fontSize.sm,
    color: colors.textSecondary,
    lineHeight: 20,
  },

  equipCard: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    backgroundColor: colors.surfaceElevated,
    borderRadius: radius.sm,
    padding: spacing.sm,
    marginBottom: 6,
  },
  equipTagBadge: {
    backgroundColor: colors.primaryDark,
    borderRadius: 4,
    paddingHorizontal: 6,
    paddingVertical: 3,
  },
  equipTagText: {
    fontSize: 10,
    fontWeight: fontWeight.bold,
    color: colors.primaryLight,
  },
  equipInfo: { flex: 1 },
  equipName: { fontSize: fontSize.xs, fontWeight: fontWeight.semibold, color: colors.textPrimary },
  equipSub: { fontSize: 10, color: colors.textMuted },

  photosScroll: { flexDirection: 'row', paddingTop: 4 },
  photoWrapper: { marginRight: spacing.sm },
  photoThumb: { width: 100, height: 100, borderRadius: radius.sm, backgroundColor: colors.surface },

  footerBar: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    backgroundColor: colors.surface,
    borderTopWidth: 1,
    borderTopColor: colors.cardBorder,
    padding: spacing.md,
    paddingBottom: spacing.lg,
    flexDirection: 'row',
    gap: spacing.sm,
    alignItems: 'center',
  },
  editBtn: {
    flex: 2,
    backgroundColor: colors.primary,
    borderRadius: radius.sm,
    paddingVertical: 12,
    alignItems: 'center',
    justifyContent: 'center',
  },
  editBtnText: { color: '#fff', fontSize: fontSize.sm, fontWeight: fontWeight.bold },
  pdfBtn: {
    flex: 1,
    backgroundColor: colors.surfaceElevated,
    borderWidth: 1,
    borderColor: colors.primaryLight,
    borderRadius: radius.sm,
    paddingVertical: 12,
    alignItems: 'center',
    justifyContent: 'center',
  },
  pdfBtnText: { color: colors.primaryLight, fontSize: fontSize.sm, fontWeight: fontWeight.bold },
  deleteBtn: {
    backgroundColor: 'rgba(239,68,68,0.15)',
    borderWidth: 1,
    borderColor: colors.danger,
    borderRadius: radius.sm,
    paddingVertical: 12,
    paddingHorizontal: 14,
    alignItems: 'center',
    justifyContent: 'center',
  },
  deleteBtnText: { fontSize: 16 },

  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.95)', justifyContent: 'center', alignItems: 'center' },
  modalCloseBtn: { position: 'absolute', top: 50, right: 20, zIndex: 10, backgroundColor: 'rgba(255,255,255,0.2)', paddingHorizontal: 12, paddingVertical: 6, borderRadius: radius.full },
  modalCloseText: { color: '#fff', fontSize: fontSize.sm, fontWeight: fontWeight.bold },
  fullImage: { width: '90%', height: '80%' },
});
