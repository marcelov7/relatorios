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
import ImagePickerSection, { LocalImage } from '../components/ImagePickerSection';
import { AppStackParamList } from '../navigation/RootNavigator';
import { fetchRelatorio, updateRelatorio } from '../services/relatorioService';
import { colors, fontSize, fontWeight, radius, spacing } from '../theme/tokens';

type Props = NativeStackScreenProps<AppStackParamList, 'RelatorioEdit'>;

const STATUS_OPTIONS = ['Aberta', 'Em Andamento', 'Concluída', 'Cancelada'] as const;

export default function RelatorioEditScreen({ route, navigation }: Props) {
  const { id } = route.params;

  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);

  // Form State
  const [titulo, setTitulo] = useState('');
  const [activity, setActivity] = useState('');
  const [detalhes, setDetalhes] = useState('');
  const [status, setStatus] = useState<(typeof STATUS_OPTIONS)[number]>('Aberta');
  const [progresso, setProgresso] = useState<number>(0);
  const [newImages, setNewImages] = useState<LocalImage[]>([]);

  useEffect(() => {
    (async () => {
      try {
        setLoading(true);
        const data = await fetchRelatorio(id);
        setTitulo(data.titulo || '');
        setActivity(data.activity || '');
        setDetalhes(data.detalhes || '');
        setStatus((data.status as any) || 'Aberta');
        setProgresso(data.progresso || 0);
      } catch (err: any) {
        Alert.alert('Erro', 'Não foi possível carregar os dados para edição.');
        navigation.goBack();
      } finally {
        setLoading(false);
      }
    })();
  }, [id]);

  const handleProgressoSelect = (val: number) => {
    setProgresso(val);
    if (val >= 100) {
      setStatus('Concluída');
    } else if (val > 0 && status === 'Aberta') {
      setStatus('Em Andamento');
    }
  };

  const handleSave = async () => {
    if (!titulo.trim()) {
      Alert.alert('Atenção', 'O título não pode ficar vazio.');
      return;
    }

    try {
      setSubmitting(true);
      const formData = new FormData();
      formData.append('titulo', titulo.trim());
      formData.append('activity', activity);
      formData.append('detalhes', detalhes.trim());
      formData.append('status', status);
      formData.append('progresso', String(progresso));

      // Novas fotos
      newImages.forEach((img, idx) => {
        formData.append(`images[${idx}]`, {
          uri: img.uri,
          name: img.name || `photo_${idx}.jpg`,
          type: img.type || 'image/jpeg',
        } as any);
      });

      await updateRelatorio(id, formData);
      Alert.alert('Sucesso', 'Relatório atualizado com sucesso!', [
        {
          text: 'OK',
          onPress: () => navigation.goBack(),
        },
      ]);
    } catch (err: any) {
      Alert.alert('Erro', err?.response?.data?.message || 'Erro ao atualizar relatório.');
    } finally {
      setSubmitting(false);
    }
  };

  if (loading) {
    return (
      <View style={styles.center}>
        <ActivityIndicator color={colors.primary} size="large" />
      </View>
    );
  }

  return (
    <KeyboardAvoidingView
      style={styles.root}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
    >
      <ScrollView contentContainerStyle={styles.content} keyboardShouldPersistTaps="handled">
        {/* Título */}
        <View style={styles.formGroup}>
          <Text style={styles.label}>Título do Relatório</Text>
          <TextInput
            style={styles.input}
            value={titulo}
            onChangeText={setTitulo}
            placeholder="Título do relatório"
            placeholderTextColor={colors.textMuted}
          />
        </View>

        {/* Evolução de Progresso (%) */}
        <View style={styles.formGroup}>
          <View style={styles.progressHeader}>
            <Text style={styles.label}>Evoluir Progresso (%)</Text>
            <Text style={styles.progressVal}>{progresso}%</Text>
          </View>
          <View style={styles.progButtonsRow}>
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
          <Text style={styles.label}>Status Atual</Text>
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

        {/* Detalhes / Atualização Técnica */}
        <View style={styles.formGroup}>
          <Text style={styles.label}>Notas Técnicas / Detalhes da Execução</Text>
          <TextInput
            style={[styles.input, styles.textArea]}
            multiline
            numberOfLines={4}
            value={detalhes}
            onChangeText={setDetalhes}
            textAlignVertical="top"
            placeholder="Atualize as observações, peças substituídas ou inspeções realizadas..."
            placeholderTextColor={colors.textMuted}
          />
        </View>

        {/* Adicionar Novas Fotos */}
        <ImagePickerSection
          images={newImages}
          onImagesChange={setNewImages}
          maxImages={10}
        />

        {/* Salvar */}
        <TouchableOpacity
          style={[styles.saveBtn, submitting && styles.saveBtnDisabled]}
          onPress={handleSave}
          disabled={submitting}
          activeOpacity={0.85}
        >
          {submitting ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <Text style={styles.saveBtnText}>💾 Salvar Alterações</Text>
          )}
        </TouchableOpacity>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.background },
  center: { flex: 1, alignItems: 'center', justifyContent: 'center', backgroundColor: colors.background },
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
  textArea: { height: 110 },

  progressHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  progressVal: { fontSize: fontSize.sm, fontWeight: fontWeight.extrabold, color: colors.primaryLight },
  progButtonsRow: { flexDirection: 'row', gap: 6, marginTop: 4 },
  progBtn: {
    flex: 1,
    backgroundColor: colors.surfaceElevated,
    borderWidth: 1,
    borderColor: colors.cardBorder,
    borderRadius: radius.sm,
    paddingVertical: 8,
    alignItems: 'center',
  },
  progBtnActive: { backgroundColor: colors.primary, borderColor: colors.primaryLight },
  progBtnText: { fontSize: fontSize.xs, fontWeight: fontWeight.semibold, color: colors.textSecondary },
  progBtnTextActive: { color: '#fff', fontWeight: fontWeight.bold },

  statusRow: { flexDirection: 'row', gap: 6 },
  statusBtn: {
    flex: 1,
    backgroundColor: colors.surfaceElevated,
    borderWidth: 1,
    borderColor: colors.cardBorder,
    borderRadius: radius.sm,
    paddingVertical: 8,
    alignItems: 'center',
  },
  statusBtnActive: { backgroundColor: colors.primary, borderColor: colors.primaryLight },
  statusBtnText: { fontSize: fontSize.xs, color: colors.textSecondary, fontWeight: fontWeight.medium },
  statusBtnTextActive: { color: '#fff', fontWeight: fontWeight.bold },

  saveBtn: {
    backgroundColor: colors.primary,
    borderRadius: radius.sm,
    paddingVertical: 14,
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: spacing.md,
  },
  saveBtnDisabled: { opacity: 0.6 },
  saveBtnText: { color: '#fff', fontSize: fontSize.md, fontWeight: fontWeight.bold },
});
