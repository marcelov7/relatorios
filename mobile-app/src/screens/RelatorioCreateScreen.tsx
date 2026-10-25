import { NativeStackScreenProps } from '@react-navigation/native-stack';
import React, { useEffect, useState } from 'react';
import {
  ActivityIndicator,
  Alert,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  TouchableOpacity,
  View,
} from 'react-native';
import EquipmentSelectorModal from '../components/EquipmentSelectorModal';
import ImagePickerSection, { LocalImage } from '../components/ImagePickerSection';
import { useAuth } from '../contexts/AuthContext';
import { AppStackParamList } from '../navigation/RootNavigator';
import {
  EquipamentoItem,
  SetorItem,
  createRelatorio,
  fetchEquipamentos,
  fetchSetores,
} from '../services/relatorioService';
import { colors, fontSize, fontWeight, radius, spacing } from '../theme/tokens';

type Props = NativeStackScreenProps<AppStackParamList, 'RelatorioCreate'>;

const ATIVIDADES = [
  'Manutenção Preventiva',
  'Manutenção Corretiva',
  'Inspeção',
  'Calibração',
  'Limpeza',
  'Treinamento',
  'Auditoria',
  'Análise',
  'Instalação',
  'Outros',
];

const STATUS_OPTIONS = ['Aberta', 'Em Andamento', 'Concluída', 'Cancelada'] as const;

export default function RelatorioCreateScreen({ navigation }: Props) {
  const { user } = useAuth();

  // Form State
  const [titulo, setTitulo] = useState('');
  const [activity, setActivity] = useState(ATIVIDADES[0]);
  const [nomeResponsavel, setNomeResponsavel] = useState(user?.name || '');
  const [cargoResponsavel, setCargoResponsavel] = useState(user?.cargo || '');
  const [detalhes, setDetalhes] = useState('');
  const [status, setStatus] = useState<(typeof STATUS_OPTIONS)[number]>('Aberta');
  const [progresso, setProgresso] = useState<number>(0);
  const [selectedSetorId, setSelectedSetorId] = useState<number | undefined>(undefined);
  const [selectedEquipmentIds, setSelectedEquipmentIds] = useState<number[]>([]);
  const [images, setImages] = useState<LocalImage[]>([]);

  // Metadata Lists
  const [setores, setSetores] = useState<SetorItem[]>([]);
  const [equipamentos, setEquipamentos] = useState<EquipamentoItem[]>([]);
  const [loadingMeta, setLoadingMeta] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [equipModalVisible, setEquipModalVisible] = useState(false);

  useEffect(() => {
    (async () => {
      try {
        setLoadingMeta(true);
        const [setoresData, equipData] = await Promise.all([
          fetchSetores(),
          fetchEquipamentos(),
        ]);
        setSetores(setoresData);
        setEquipamentos(equipData);
        if (setoresData.length > 0) {
          setSelectedSetorId(setoresData[0].id);
        }
      } catch (err) {
        console.error('Erro ao carregar dados de apoio:', err);
      } finally {
        setLoadingMeta(false);
      }
    })();
  }, []);

  const handleProgressoSelect = (val: number) => {
    setProgresso(val);
    if (val >= 100) {
      setStatus('Concluída');
    } else if (val > 0 && status === 'Aberta') {
      setStatus('Em Andamento');
    }
  };

  const handleSubmit = async () => {
    if (!titulo.trim()) {
      Alert.alert('Atenção', 'Informe o título do relatório.');
      return;
    }
    if (!detalhes.trim()) {
      Alert.alert('Atenção', 'Descreva os detalhes da atividade.');
      return;
    }

    try {
      setSubmitting(true);
      const formData = new FormData();
      formData.append('titulo', titulo.trim());
      formData.append('activity', activity);
      formData.append('nome_responsavel', nomeResponsavel);
      formData.append('cargo_responsavel', cargoResponsavel);
      formData.append('detalhes', detalhes.trim());
      formData.append('status', status);
      formData.append('progresso', String(progresso));

      if (selectedSetorId) {
        formData.append('setor_id', String(selectedSetorId));
      }

      selectedEquipmentIds.forEach((id, idx) => {
        formData.append(`equipment_ids[${idx}]`, String(id));
      });

      // Anexar fotos
      images.forEach((img, idx) => {
        formData.append(`images[${idx}]`, {
          uri: img.uri,
          name: img.name || `photo_${idx}.jpg`,
          type: img.type || 'image/jpeg',
        } as any);
      });

      const created = await createRelatorio(formData);
      Alert.alert('Sucesso', 'Relatório criado com sucesso!', [
        {
          text: 'OK',
          onPress: () => {
            navigation.navigate('RelatorioDetail', { id: created.id });
          },
        },
      ]);
    } catch (err: any) {
      const msg =
        err?.response?.data?.message ||
        'Erro ao salvar o relatório. Verifique sua conexão e tente novamente.';
      Alert.alert('Erro', msg);
    } finally {
      setSubmitting(false);
    }
  };

  const selectedEquips = equipamentos.filter((e) => selectedEquipmentIds.includes(e.id));

  return (
    <KeyboardAvoidingView
      style={styles.root}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
    >
      <ScrollView contentContainerStyle={styles.content} keyboardShouldPersistTaps="handled">
        {/* Título */}
        <View style={styles.formGroup}>
          <Text style={styles.label}>Título do Relatório *</Text>
          <TextInput
            style={styles.input}
            placeholder="Ex: Inspeção Semanal do Gerador A"
            placeholderTextColor={colors.textMuted}
            value={titulo}
            onChangeText={setTitulo}
          />
        </View>

        {/* Atividade */}
        <View style={styles.formGroup}>
          <Text style={styles.label}>Tipo de Atividade *</Text>
          <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.chipsRow}>
            {ATIVIDADES.map((act) => {
              const active = activity === act;
              return (
                <TouchableOpacity
                  key={act}
                  style={[styles.chip, active && styles.chipActive]}
                  onPress={() => setActivity(act)}
                >
                  <Text style={[styles.chipText, active && styles.chipTextActive]}>{act}</Text>
                </TouchableOpacity>
              );
            })}
          </ScrollView>
        </View>

        {/* Setor */}
        <View style={styles.formGroup}>
          <Text style={styles.label}>Setor Operacional</Text>
          {loadingMeta ? (
            <ActivityIndicator size="small" color={colors.primary} />
          ) : (
            <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.chipsRow}>
              {setores.map((s) => {
                const active = selectedSetorId === s.id;
                return (
                  <TouchableOpacity
                    key={s.id}
                    style={[styles.chip, active && styles.chipActive]}
                    onPress={() => setSelectedSetorId(s.id)}
                  >
                    <Text style={[styles.chipText, active && styles.chipTextActive]}>{s.nome}</Text>
                  </TouchableOpacity>
                );
              })}
            </ScrollView>
          )}
        </View>

        {/* Equipamentos Vinculados */}
        <View style={styles.formGroup}>
          <View style={styles.equipHeader}>
            <Text style={styles.label}>Equipamentos Vinculados</Text>
            <TouchableOpacity onPress={() => setEquipModalVisible(true)} style={styles.selectEquipBtn}>
              <Text style={styles.selectEquipText}>+ Selecionar ({selectedEquipmentIds.length})</Text>
            </TouchableOpacity>
          </View>

          {selectedEquips.length > 0 ? (
            <View style={styles.selectedEquipContainer}>
              {selectedEquips.map((eq) => (
                <View key={eq.id} style={styles.selectedEquipBadge}>
                  <Text style={styles.selectedEquipTag}>{eq.equipment_tag || 'EQ'}</Text>
                  <Text style={styles.selectedEquipName} numberOfLines={1}>
                    {eq.nome}
                  </Text>
                  <TouchableOpacity
                    onPress={() => setSelectedEquipmentIds(selectedEquipmentIds.filter((id) => id !== eq.id))}
                  >
                    <Text style={styles.removeEquip}>✕</Text>
                  </TouchableOpacity>
                </View>
              ))}
            </View>
          ) : (
            <TouchableOpacity
              style={styles.emptyEquipCard}
              onPress={() => setEquipModalVisible(true)}
            >
              <Text style={styles.emptyEquipText}>Toque aqui para vincular equipamentos</Text>
            </TouchableOpacity>
          )}
        </View>

        {/* Progresso Interativo */}
        <View style={styles.formGroup}>
          <View style={styles.progressHeaderRow}>
            <Text style={styles.label}>Progresso da Atividade</Text>
            <Text style={styles.progressValueText}>{progresso}%</Text>
          </View>
          <View style={styles.progressButtonsRow}>
            {[0, 25, 50, 75, 100].map((val) => {
              const active = progresso === val;
              return (
                <TouchableOpacity
                  key={val}
                  style={[styles.progBtn, active && styles.progBtnActive]}
                  onPress={() => handleProgressoSelect(val)}
                >
                  <Text style={[styles.progBtnText, active && styles.progBtnTextActive]}>
                    {val}%
                  </Text>
                </TouchableOpacity>
              );
            })}
          </View>
        </View>

        {/* Status */}
        <View style={styles.formGroup}>
          <Text style={styles.label}>Status do Relatório</Text>
          <View style={styles.statusRow}>
            {STATUS_OPTIONS.map((st) => {
              const active = status === st;
              return (
                <TouchableOpacity
                  key={st}
                  style={[styles.statusBtn, active && styles.statusBtnActive]}
                  onPress={() => setStatus(st)}
                >
                  <Text style={[styles.statusBtnText, active && styles.statusBtnTextActive]}>
                    {st}
                  </Text>
                </TouchableOpacity>
              );
            })}
          </View>
        </View>

        {/* Detalhes Técnicos */}
        <View style={styles.formGroup}>
          <Text style={styles.label}>Descrição / Detalhes da Execução *</Text>
          <TextInput
            style={[styles.input, styles.textArea]}
            placeholder="Descreva as verificações realizadas, anomalias encontradas, peças trocadas ou observações..."
            placeholderTextColor={colors.textMuted}
            multiline
            numberOfLines={4}
            value={detalhes}
            onChangeText={setDetalhes}
            textAlignVertical="top"
          />
        </View>

        {/* Seção de Fotos (Câmera + Galeria) */}
        <ImagePickerSection images={images} onImagesChange={setImages} maxImages={10} />

        {/* Botão de Envio */}
        <TouchableOpacity
          style={[styles.submitBtn, submitting && styles.submitBtnDisabled]}
          onPress={handleSubmit}
          disabled={submitting}
          activeOpacity={0.85}
        >
          {submitting ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <Text style={styles.submitBtnText}>💾 Salvar Relatório</Text>
          )}
        </TouchableOpacity>
      </ScrollView>

      {/* Modal de Seleção de Equipamentos */}
      <EquipmentSelectorModal
        visible={equipModalVisible}
        onClose={() => setEquipModalVisible(false)}
        equipamentos={equipamentos}
        selectedIds={selectedEquipmentIds}
        onSelect={setSelectedEquipmentIds}
      />
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.background },
  content: { padding: spacing.md, paddingBottom: spacing.xxl },

  formGroup: { marginBottom: spacing.md },
  label: {
    fontSize: fontSize.sm,
    fontWeight: fontWeight.bold,
    color: colors.textPrimary,
    marginBottom: 6,
  },
  input: {
    backgroundColor: colors.inputBg,
    borderWidth: 1,
    borderColor: colors.inputBorder,
    borderRadius: radius.sm,
    paddingHorizontal: spacing.sm,
    paddingVertical: 10,
    fontSize: fontSize.md,
    color: colors.textPrimary,
  },
  textArea: {
    height: 100,
  },

  chipsRow: {
    flexDirection: 'row',
  },
  chip: {
    backgroundColor: colors.surfaceElevated,
    borderWidth: 1,
    borderColor: colors.cardBorder,
    borderRadius: radius.full,
    paddingHorizontal: spacing.md,
    paddingVertical: 7,
    marginRight: spacing.xs,
  },
  chipActive: {
    backgroundColor: colors.primary,
    borderColor: colors.primaryLight,
  },
  chipText: {
    fontSize: fontSize.xs,
    color: colors.textSecondary,
    fontWeight: fontWeight.medium,
  },
  chipTextActive: {
    color: '#fff',
    fontWeight: fontWeight.bold,
  },

  equipHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 6,
  },
  selectEquipBtn: {
    paddingVertical: 2,
    paddingHorizontal: 8,
    backgroundColor: 'rgba(27, 79, 138, 0.25)',
    borderRadius: radius.sm,
  },
  selectEquipText: {
    fontSize: fontSize.xs,
    color: colors.primaryLight,
    fontWeight: fontWeight.bold,
  },
  selectedEquipContainer: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 6,
  },
  selectedEquipBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    backgroundColor: colors.card,
    borderWidth: 1,
    borderColor: colors.cardBorder,
    borderRadius: radius.sm,
    paddingHorizontal: spacing.sm,
    paddingVertical: 6,
  },
  selectedEquipTag: {
    fontSize: 10,
    fontWeight: fontWeight.bold,
    color: colors.primaryLight,
    backgroundColor: colors.primaryDark,
    paddingHorizontal: 4,
    paddingVertical: 1,
    borderRadius: 3,
  },
  selectedEquipName: {
    fontSize: fontSize.xs,
    color: colors.textPrimary,
    maxWidth: 160,
  },
  removeEquip: {
    color: colors.danger,
    fontSize: 12,
    fontWeight: fontWeight.bold,
    paddingLeft: 4,
  },
  emptyEquipCard: {
    borderWidth: 1,
    borderStyle: 'dashed',
    borderColor: colors.inputBorder,
    borderRadius: radius.sm,
    padding: spacing.md,
    alignItems: 'center',
  },
  emptyEquipText: {
    fontSize: fontSize.xs,
    color: colors.textMuted,
  },

  progressHeaderRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  progressValueText: {
    fontSize: fontSize.sm,
    fontWeight: fontWeight.extrabold,
    color: colors.primaryLight,
  },
  progressButtonsRow: {
    flexDirection: 'row',
    gap: 6,
    marginTop: 4,
  },
  progBtn: {
    flex: 1,
    backgroundColor: colors.surfaceElevated,
    borderWidth: 1,
    borderColor: colors.cardBorder,
    borderRadius: radius.sm,
    paddingVertical: 8,
    alignItems: 'center',
  },
  progBtnActive: {
    backgroundColor: colors.primary,
    borderColor: colors.primaryLight,
  },
  progBtnText: {
    fontSize: fontSize.xs,
    fontWeight: fontWeight.semibold,
    color: colors.textSecondary,
  },
  progBtnTextActive: {
    color: '#fff',
    fontWeight: fontWeight.bold,
  },

  statusRow: {
    flexDirection: 'row',
    gap: 6,
  },
  statusBtn: {
    flex: 1,
    backgroundColor: colors.surfaceElevated,
    borderWidth: 1,
    borderColor: colors.cardBorder,
    borderRadius: radius.sm,
    paddingVertical: 8,
    alignItems: 'center',
  },
  statusBtnActive: {
    backgroundColor: colors.primary,
    borderColor: colors.primaryLight,
  },
  statusBtnText: {
    fontSize: fontSize.xs,
    color: colors.textSecondary,
    fontWeight: fontWeight.medium,
  },
  statusBtnTextActive: {
    color: '#fff',
    fontWeight: fontWeight.bold,
  },

  submitBtn: {
    backgroundColor: colors.primary,
    borderRadius: radius.sm,
    paddingVertical: 14,
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: spacing.md,
    shadowColor: colors.primary,
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.4,
    shadowRadius: 10,
    elevation: 6,
  },
  submitBtnDisabled: { opacity: 0.6 },
  submitBtnText: {
    color: '#fff',
    fontSize: fontSize.md,
    fontWeight: fontWeight.bold,
    letterSpacing: 0.5,
  },
});
