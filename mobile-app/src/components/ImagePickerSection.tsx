import * as ImagePicker from 'expo-image-picker';
import React from 'react';
import {
  Alert,
  Image,
  ScrollView,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from 'react-native';
import { colors, fontSize, fontWeight, radius, spacing } from '../theme/tokens';

export interface LocalImage {
  uri: string;
  name?: string;
  type?: string;
}

interface Props {
  images: LocalImage[];
  onImagesChange: (images: LocalImage[]) => void;
  maxImages?: number;
}

export default function ImagePickerSection({
  images,
  onImagesChange,
  maxImages = 10,
}: Props) {
  const handlePickFromGallery = async () => {
    if (images.length >= maxImages) {
      Alert.alert('Limite atingido', `Você pode anexar no máximo ${maxImages} fotos.`);
      return;
    }

    const { status } = await ImagePicker.requestMediaLibraryPermissionsAsync();
    if (status !== 'granted') {
      Alert.alert('Permissão necessária', 'Permita o acesso à galeria para anexar fotos.');
      return;
    }

    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ['images'],
      allowsMultipleSelection: true,
      quality: 0.8,
      selectionLimit: maxImages - images.length,
    });

    if (!result.canceled && result.assets) {
      const newImgs: LocalImage[] = result.assets.map((asset, i) => {
        const uriParts = asset.uri.split('/');
        const fileName = asset.fileName || uriParts[uriParts.length - 1] || `photo_${Date.now()}_${i}.jpg`;
        return {
          uri: asset.uri,
          name: fileName,
          type: asset.mimeType || 'image/jpeg',
        };
      });
      onImagesChange([...images, ...newImgs]);
    }
  };

  const handleTakePhoto = async () => {
    if (images.length >= maxImages) {
      Alert.alert('Limite atingido', `Você pode anexar no máximo ${maxImages} fotos.`);
      return;
    }

    const { status } = await ImagePicker.requestCameraPermissionsAsync();
    if (status !== 'granted') {
      Alert.alert('Permissão necessária', 'Permita o acesso à câmera para tirar fotos.');
      return;
    }

    const result = await ImagePicker.launchCameraAsync({
      quality: 0.8,
      allowsEditing: false,
    });

    if (!result.canceled && result.assets && result.assets.length > 0) {
      const asset = result.assets[0];
      const uriParts = asset.uri.split('/');
      const fileName = asset.fileName || uriParts[uriParts.length - 1] || `camera_${Date.now()}.jpg`;
      const newImg: LocalImage = {
        uri: asset.uri,
        name: fileName,
        type: asset.mimeType || 'image/jpeg',
      };
      onImagesChange([...images, newImg]);
    }
  };

  const handleRemoveImage = (index: number) => {
    const updated = [...images];
    updated.splice(index, 1);
    onImagesChange(updated);
  };

  return (
    <View style={styles.container}>
      <View style={styles.headerRow}>
        <Text style={styles.sectionLabel}>📷 Fotos e Evidências</Text>
        <Text style={styles.countText}>
          {images.length}/{maxImages}
        </Text>
      </View>

      {/* Botões de Ação */}
      <View style={styles.buttonsRow}>
        <TouchableOpacity style={styles.actionBtn} onPress={handleTakePhoto} activeOpacity={0.8}>
          <Text style={styles.actionIcon}>📸</Text>
          <Text style={styles.actionText}>Tirar Foto</Text>
        </TouchableOpacity>

        <TouchableOpacity style={styles.actionBtn} onPress={handlePickFromGallery} activeOpacity={0.8}>
          <Text style={styles.actionIcon}>🖼️</Text>
          <Text style={styles.actionText}>Galeria</Text>
        </TouchableOpacity>
      </View>

      {/* Grid de Fotos Selecionadas */}
      {images.length > 0 ? (
        <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.previewsContainer}>
          {images.map((img, index) => (
            <View key={`${img.uri}-${index}`} style={styles.thumbnailWrapper}>
              <Image source={{ uri: img.uri }} style={styles.thumbnail} />
              <TouchableOpacity
                style={styles.removeBadge}
                onPress={() => handleRemoveImage(index)}
                hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}
              >
                <Text style={styles.removeText}>✕</Text>
              </TouchableOpacity>
            </View>
          ))}
        </ScrollView>
      ) : (
        <View style={styles.emptyState}>
          <Text style={styles.emptyIcon}>📷</Text>
          <Text style={styles.emptyText}>Nenhuma foto anexada ainda</Text>
        </View>
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    marginVertical: spacing.sm,
  },
  headerRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: spacing.xs,
  },
  sectionLabel: {
    fontSize: fontSize.sm,
    fontWeight: fontWeight.bold,
    color: colors.textPrimary,
  },
  countText: {
    fontSize: fontSize.xs,
    color: colors.textSecondary,
  },
  buttonsRow: {
    flexDirection: 'row',
    gap: spacing.sm,
    marginBottom: spacing.sm,
  },
  actionBtn: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 6,
    backgroundColor: colors.surfaceElevated,
    borderWidth: 1,
    borderColor: colors.cardBorder,
    borderRadius: radius.sm,
    paddingVertical: 10,
  },
  actionIcon: {
    fontSize: 16,
  },
  actionText: {
    fontSize: fontSize.sm,
    fontWeight: fontWeight.semibold,
    color: colors.textPrimary,
  },
  previewsContainer: {
    flexDirection: 'row',
    paddingVertical: spacing.xs,
  },
  thumbnailWrapper: {
    position: 'relative',
    marginRight: spacing.sm,
  },
  thumbnail: {
    width: 80,
    height: 80,
    borderRadius: radius.sm,
    backgroundColor: colors.surface,
  },
  removeBadge: {
    position: 'absolute',
    top: -6,
    right: -6,
    width: 22,
    height: 22,
    borderRadius: 11,
    backgroundColor: colors.danger,
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 1.5,
    borderColor: colors.background,
  },
  removeText: {
    color: '#fff',
    fontSize: 11,
    fontWeight: fontWeight.bold,
  },
  emptyState: {
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: colors.inputBg,
    borderWidth: 1,
    borderColor: colors.inputBorder,
    borderStyle: 'dashed',
    borderRadius: radius.sm,
    paddingVertical: spacing.md,
  },
  emptyIcon: {
    fontSize: 22,
    marginBottom: 2,
    opacity: 0.6,
  },
  emptyText: {
    fontSize: fontSize.xs,
    color: colors.textMuted,
  },
});
