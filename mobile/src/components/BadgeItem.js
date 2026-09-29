import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { THEME } from '../constants/theme';

export function BadgeItem({ badge }) {
  const isUnlocked = badge.unlocked;

  const getIconName = (category, icon) => {
    if (icon === 'star') return 'star';
    if (icon === 'award') return 'ribbon-outline';
    if (icon === 'calendar') return 'calendar-outline';
    if (icon === 'check-circle') return 'checkmark-circle-outline';
    if (icon === 'bolt') return 'flash-outline';
    if (icon === 'activity') return 'fitness-outline';
    if (icon === 'trophy') return 'trophy-outline';
    return 'disc-outline';
  };

  return (
    <View style={[styles.card, !isUnlocked && styles.cardLocked]}>
      <View style={[styles.iconCircle, isUnlocked ? styles.iconCircleUnlocked : styles.iconCircleLocked]}>
        <Ionicons
          name={getIconName(badge.kategori, badge.icon)}
          size={22}
          color={isUnlocked ? THEME.colors.primary : THEME.colors.textMuted}
        />
      </View>

      <View style={styles.content}>
        <View style={styles.titleRow}>
          <Text style={[styles.title, !isUnlocked && styles.titleLocked]} numberOfLines={1}>
            {badge.nama}
          </Text>
          <View style={[styles.pill, isUnlocked ? styles.pillUnlocked : styles.pillLocked]}>
            <Text style={[styles.pillText, isUnlocked ? styles.pillTextUnlocked : styles.pillTextLocked]}>
              {isUnlocked ? 'Terbuka' : 'Terkunci'}
            </Text>
          </View>
        </View>

        <Text style={styles.description} numberOfLines={2}>
          {badge.deskripsi}
        </Text>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  card: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: THEME.colors.surface,
    padding: THEME.spacing.md,
    borderRadius: THEME.borderRadius.lg,
    borderWidth: 1,
    borderColor: THEME.colors.border,
    marginBottom: THEME.spacing.sm,
    ...THEME.shadow.sm,
  },
  cardLocked: {
    backgroundColor: THEME.colors.surfaceVariant,
    opacity: 0.75,
  },
  iconCircle: {
    width: 44,
    height: 44,
    borderRadius: THEME.borderRadius.full,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: THEME.spacing.md,
  },
  iconCircleUnlocked: {
    backgroundColor: THEME.colors.primaryMuted,
    borderWidth: 1,
    borderColor: THEME.colors.primary + '33',
  },
  iconCircleLocked: {
    backgroundColor: THEME.colors.border,
  },
  content: {
    flex: 1,
  },
  titleRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 2,
  },
  title: {
    fontSize: THEME.typography.sizes.sm,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.text,
    flex: 1,
  },
  titleLocked: {
    color: THEME.colors.textSecondary,
  },
  pill: {
    paddingHorizontal: THEME.spacing.sm,
    paddingVertical: 2,
    borderRadius: THEME.borderRadius.sm,
  },
  pillUnlocked: {
    backgroundColor: THEME.colors.primaryMuted,
  },
  pillLocked: {
    backgroundColor: THEME.colors.border,
  },
  pillText: {
    fontSize: 10,
    fontWeight: THEME.typography.weights.semibold,
  },
  pillTextUnlocked: {
    color: THEME.colors.primary,
  },
  pillTextLocked: {
    color: THEME.colors.textMuted,
  },
  description: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textSecondary,
    lineHeight: 16,
  }
});
