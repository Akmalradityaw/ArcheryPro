import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  RefreshControl,
  ActivityIndicator,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { THEME } from '../constants/theme';
import { useAuth } from '../context/AuthContext';
import { ApiService } from '../services/api';
import { CardStat } from '../components/CardStat';

export function DashboardScreen({ navigation }) {
  const { user, role, atlet } = useAuth();
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [sesiList, setSesiList] = useState([]);
  const [performaData, setPerformaData] = useState(null);
  const [unreadNote, setUnreadNote] = useState(null);
  const [leaderboard, setLeaderboard] = useState([]);
  const [errorMsg, setErrorMsg] = useState(null);

  const loadData = useCallback(async () => {
    setErrorMsg(null);
    try {
      // 1. Ambil daftar sesi latihan mingguan (10 data)
      const sesiRes = await ApiService.get('/sesi-latihan');
      const sesiData = Array.isArray(sesiRes?.data) ? sesiRes.data : (sesiRes?.data?.data || []);
      setSesiList(sesiData.slice(0, 5)); // Tampilkan 5 sesi teratas di dashboard

      // 2. Jika atlet, ambil performa atlet
      if (role === 'atlet' && atlet?.id) {
        const perfRes = await ApiService.get(`/atlet/${atlet.id}/performa`);
        if (perfRes?.data) {
          setPerformaData(perfRes.data);
          setUnreadNote(perfRes.data.catatan_pelatih_baru || null);
        }
      }

      // 3. Ambil top leaderboard publik
      const leadRes = await ApiService.get('/public/leaderboard').catch(() => null);
      if (leadRes?.data) {
        setLeaderboard(leadRes.data.slice(0, 5));
      }
    } catch (err) {
      console.warn('Dashboard load data error:', err);
      setErrorMsg(err.message || 'Gagal memuat data dashboard.');
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, [role, atlet]);

  useEffect(() => {
    loadData();
  }, [loadData]);

  const onRefresh = () => {
    setRefreshing(true);
    loadData();
  };

  const handleMarkNoteRead = async (sesiId) => {
    try {
      await ApiService.post(`/sesi/${sesiId}/baca-catatan`);
      setUnreadNote(null);
    } catch (err) {
      console.warn('Gagal menandai catatan dibaca:', err);
    }
  };

  const getStatusBadge = (status) => {
    let bg = THEME.colors.surfaceVariant;
    let color = THEME.colors.textSecondary;
    let label = status;

    if (status === 'berlangsung') {
      bg = THEME.colors.primaryMuted;
      color = THEME.colors.primary;
      label = 'Berlangsung';
    } else if (status === 'terjadwal') {
      bg = THEME.colors.infoMuted;
      color = THEME.colors.info;
      label = 'Terjadwal';
    } else if (status === 'selesai') {
      bg = THEME.colors.border;
      color = THEME.colors.textMuted;
      label = 'Selesai';
    }

    return (
      <View style={[styles.statusPill, { backgroundColor: bg }]}>
        <Text style={[styles.statusText, { color }]}>{label}</Text>
      </View>
    );
  };

  return (
    <ScrollView
      style={styles.container}
      contentContainerStyle={styles.contentContainer}
      refreshControl={
        <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[THEME.colors.primary]} />
      }
    >
      {/* Top Welcome Banner */}
      <View style={styles.welcomeCard}>
        <View style={styles.welcomeRow}>
          <View style={styles.welcomeInfo}>
            <Text style={styles.welcomeRole}>
              {role === 'pelatih' ? 'Pelatih Panahan' : (role === 'admin' ? 'Administrator' : 'Atlet Panahan')}
            </Text>
            <Text style={styles.welcomeName} numberOfLines={1}>
              {user?.nama_lengkap || user?.username || 'Pengguna'}
            </Text>
            {atlet?.sekolah?.nama_sekolah && (
              <Text style={styles.welcomeSub}>{atlet.sekolah.nama_sekolah}</Text>
            )}
          </View>
          <View style={styles.avatarCircle}>
            <Ionicons
              name={role === 'pelatih' ? 'school-outline' : 'person-outline'}
              size={24}
              color={THEME.colors.surface}
            />
          </View>
        </View>
      </View>

      {/* Unread Coach Note Alert Banner (Khusus Atlet) */}
      {unreadNote && (
        <View style={styles.alertBanner}>
          <View style={styles.alertHeader}>
            <View style={styles.alertTitleRow}>
              <Ionicons name="chatbubble-ellipses" size={18} color={THEME.colors.warning} />
              <Text style={styles.alertTitle}>Catatan Evaluasi Baru</Text>
            </View>
            <TouchableOpacity
              onPress={() => handleMarkNoteRead(unreadNote.sesi_id)}
              style={styles.alertDismissBtn}
              activeOpacity={0.7}
            >
              <Text style={styles.alertDismissText}>Tandai Dibaca</Text>
            </TouchableOpacity>
          </View>
          <Text style={styles.alertSesiInfo}>{unreadNote.sesi_nama} • {unreadNote.tanggal}</Text>
          <Text style={styles.alertContent}>"{unreadNote.catatan}"</Text>
        </View>
      )}

      {/* Error Feedback */}
      {errorMsg && (
        <View style={styles.errorBox}>
          <Ionicons name="alert-circle-outline" size={18} color={THEME.colors.error} />
          <Text style={styles.errorText}>{errorMsg}</Text>
        </View>
      )}

      {/* Loading Indicator */}
      {loading ? (
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color={THEME.colors.primary} />
          <Text style={styles.loadingText}>Memuat ringkasan performa...</Text>
        </View>
      ) : (
        <>
          {/* Quick Stats Grid */}
          <Text style={styles.sectionTitle}>Ringkasan Aktivitas</Text>
          <View style={styles.statsGrid}>
            {role === 'atlet' && performaData ? (
              <>
                <View style={styles.gridItem}>
                  <CardStat
                    title="Total Sesi"
                    value={performaData.statistik?.total_sesi ?? 0}
                    subtitle="Sesi Latihan"
                    icon="calendar-outline"
                    iconColor={THEME.colors.primary}
                  />
                </View>
                <View style={styles.gridItem}>
                  <CardStat
                    title="Rata-rata Skor"
                    value={performaData.statistik?.rata_rata_skor ?? 0}
                    subtitle="Poin per sesi"
                    icon="trending-up-outline"
                    iconColor={THEME.colors.primary}
                  />
                </View>
                <View style={styles.gridItem}>
                  <CardStat
                    title="Skor Terbaik"
                    value={performaData.statistik?.skor_tertinggi ?? 0}
                    subtitle="Rekor pribadi"
                    icon="trophy-outline"
                    iconColor={THEME.colors.badgeGold}
                  />
                </View>
                <View style={styles.gridItem}>
                  <CardStat
                    title="Konsistensi"
                    value={performaData.stabilitas?.kategori_stabilitas ?? 'Belum ada'}
                    subtitle={performaData.stabilitas ? `SD ±${performaData.stabilitas.standar_deviasi}` : 'Min. 2 end'}
                    icon="shield-checkmark-outline"
                    iconColor={THEME.colors.info}
                  />
                </View>
              </>
            ) : (
              <>
                <View style={styles.gridItem}>
                  <CardStat
                    title="Sesi Latihan"
                    value={sesiList.length}
                    subtitle="Terjadwal aktif"
                    icon="calendar-outline"
                    iconColor={THEME.colors.primary}
                  />
                </View>
                <View style={styles.gridItem}>
                  <CardStat
                    title="Input Skor"
                    value="Cepat"
                    subtitle="Multi-atlet"
                    icon="create-outline"
                    iconColor={THEME.colors.info}
                  />
                </View>
              </>
            )}
          </View>

          {/* Quick Action Navigation Buttons */}
          <View style={styles.quickActionRow}>
            {(role === 'pelatih' || role === 'admin') && (
              <TouchableOpacity
                style={[styles.actionBtn, styles.actionBtnPrimary]}
                onPress={() => navigation.navigate('InputSkor')}
                activeOpacity={0.8}
              >
                <Ionicons name="add-circle-outline" size={20} color={THEME.colors.surface} />
                <Text style={styles.actionBtnTextPrimary}>Input Skor Latihan</Text>
              </TouchableOpacity>
            )}

            <TouchableOpacity
              style={[styles.actionBtn, styles.actionBtnSecondary]}
              onPress={() => navigation.navigate('SesiLatihan')}
              activeOpacity={0.8}
            >
              <Ionicons name="list-outline" size={20} color={THEME.colors.primary} />
              <Text style={styles.actionBtnTextSecondary}>Jadwal Mingguan</Text>
            </TouchableOpacity>

            {role === 'atlet' && (
              <TouchableOpacity
                style={[styles.actionBtn, styles.actionBtnSecondary]}
                onPress={() => navigation.navigate('Performa')}
                activeOpacity={0.8}
              >
                <Ionicons name="bar-chart-outline" size={20} color={THEME.colors.primary} />
                <Text style={styles.actionBtnTextSecondary}>Detail Performa</Text>
              </TouchableOpacity>
            )}
          </View>

          {/* Sesi Latihan Terkini */}
          <View style={styles.sectionHeaderRow}>
            <Text style={styles.sectionTitle}>Sesi Latihan Terkini</Text>
            <TouchableOpacity onPress={() => navigation.navigate('SesiLatihan')}>
              <Text style={styles.seeAllText}>Lihat Semua</Text>
            </TouchableOpacity>
          </View>

          {sesiList.length === 0 ? (
            <View style={styles.emptyCard}>
              <Ionicons name="calendar-outline" size={36} color={THEME.colors.textMuted} />
              <Text style={styles.emptyText}>Belum ada jadwal sesi latihan aktif.</Text>
            </View>
          ) : (
            sesiList.map((item) => (
              <TouchableOpacity
                key={item.id}
                style={styles.sesiCard}
                activeOpacity={0.7}
                onPress={() => navigation.navigate('SesiLatihan', { selectedId: item.id })}
              >
                <View style={styles.sesiCardLeft}>
                  <View style={[styles.calendarBadge, item.is_today && styles.calendarBadgeToday]}>
                    <Text style={[styles.calendarBadgeDay, item.is_today && styles.calendarBadgeDayToday]}>
                      {item.tanggal ? item.tanggal.split('-')[2] : '--'}
                    </Text>
                    <Text style={[styles.calendarBadgeMonth, item.is_today && styles.calendarBadgeMonthToday]}>
                      {item.is_today ? 'HARI INI' : 'SESI'}
                    </Text>
                  </View>
                  <View style={styles.sesiCardInfo}>
                    <Text style={styles.sesiCardTitle} numberOfLines={1}>{item.nama_sesi}</Text>
                    <Text style={styles.sesiCardSub} numberOfLines={1}>
                      {item.jam_mulai ? `${item.jam_mulai} - ${item.jam_selesai || 'selesai'}` : item.tanggal_formatted} • {item.lokasi || 'Lapangan Utama'}
                    </Text>
                    <Text style={styles.sesiCardPeserta}>
                      <Ionicons name="people-outline" size={13} color={THEME.colors.textSecondary} /> {item.total_peserta || 0} atlet tercatat
                    </Text>
                  </View>
                </View>
                <View style={styles.sesiCardRight}>
                  {getStatusBadge(item.status)}
                  <Ionicons name="chevron-forward" size={16} color={THEME.colors.textMuted} style={{ marginTop: 6 }} />
                </View>
              </TouchableOpacity>
            ))
          )}

          {/* Top Leaderboard Publik */}
          {leaderboard.length > 0 && (
            <View style={{ marginTop: THEME.spacing.lg }}>
              <View style={styles.sectionHeaderRow}>
                <Text style={styles.sectionTitle}>Top Atlet (Papan Publik)</Text>
              </View>
              <View style={styles.leaderboardCard}>
                {leaderboard.map((item, idx) => (
                  <View key={item.id || idx} style={[styles.leaderboardRow, idx > 0 && styles.leaderboardDivider]}>
                    <View style={[
                      styles.rankCircle,
                      idx === 0 ? styles.rank1 : (idx === 1 ? styles.rank2 : (idx === 2 ? styles.rank3 : styles.rankDefault))
                    ]}>
                      <Text style={[styles.rankText, idx < 3 && styles.rankTextTop]}>{idx + 1}</Text>
                    </View>
                    <View style={styles.leaderboardInfo}>
                      <Text style={styles.leaderboardName} numberOfLines={1}>{item.nama_lengkap}</Text>
                      <Text style={styles.leaderboardSchool} numberOfLines={1}>{item.sekolah || item.kategori || '-'}</Text>
                    </View>
                    <View style={styles.leaderboardScoreBox}>
                      <Text style={styles.leaderboardScore}>{item.skor_tertinggi || item.total_skor || 0}</Text>
                      <Text style={styles.leaderboardScoreLabel}>Poin Tertinggi</Text>
                    </View>
                  </View>
                ))}
              </View>
            </View>
          )}
        </>
      )}
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: THEME.colors.background,
  },
  contentContainer: {
    padding: THEME.spacing.lg,
    paddingBottom: 40,
  },
  welcomeCard: {
    backgroundColor: THEME.colors.primary,
    borderRadius: THEME.borderRadius.lg,
    padding: THEME.spacing.lg,
    marginBottom: THEME.spacing.lg,
    ...THEME.shadow.md,
  },
  welcomeRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  welcomeInfo: {
    flex: 1,
    paddingRight: THEME.spacing.md,
  },
  welcomeRole: {
    fontSize: THEME.typography.sizes.xs,
    fontWeight: THEME.typography.weights.semibold,
    color: '#E8F5E9',
    textTransform: 'uppercase',
    letterSpacing: 0.5,
  },
  welcomeName: {
    fontSize: THEME.typography.sizes.xl,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.surface,
    marginTop: 2,
  },
  welcomeSub: {
    fontSize: THEME.typography.sizes.xs,
    color: '#C8E6C9',
    marginTop: 2,
  },
  avatarCircle: {
    width: 48,
    height: 48,
    borderRadius: THEME.borderRadius.full,
    backgroundColor: THEME.colors.primaryDark,
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 2,
    borderColor: '#81C784',
  },
  alertBanner: {
    backgroundColor: THEME.colors.warningMuted,
    borderWidth: 1,
    borderColor: THEME.colors.warning + '44',
    borderRadius: THEME.borderRadius.md,
    padding: THEME.spacing.md,
    marginBottom: THEME.spacing.lg,
  },
  alertHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 4,
  },
  alertTitleRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  alertTitle: {
    fontSize: THEME.typography.sizes.sm,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.warning,
  },
  alertDismissBtn: {
    backgroundColor: THEME.colors.surface,
    paddingHorizontal: THEME.spacing.sm,
    paddingVertical: 3,
    borderRadius: THEME.borderRadius.sm,
    borderWidth: 1,
    borderColor: THEME.colors.warning + '55',
  },
  alertDismissText: {
    fontSize: 10,
    fontWeight: THEME.typography.weights.semibold,
    color: THEME.colors.warning,
  },
  alertSesiInfo: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textSecondary,
    marginBottom: 4,
  },
  alertContent: {
    fontSize: THEME.typography.sizes.sm,
    color: THEME.colors.text,
    fontStyle: 'italic',
  },
  errorBox: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    backgroundColor: THEME.colors.errorMuted,
    padding: THEME.spacing.md,
    borderRadius: THEME.borderRadius.md,
    marginBottom: THEME.spacing.lg,
  },
  errorText: {
    flex: 1,
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.error,
  },
  loadingContainer: {
    paddingVertical: 40,
    alignItems: 'center',
    justifyContent: 'center',
  },
  loadingText: {
    fontSize: THEME.typography.sizes.sm,
    color: THEME.colors.textSecondary,
    marginTop: THEME.spacing.sm,
  },
  sectionTitle: {
    fontSize: THEME.typography.sizes.md,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.text,
    marginBottom: THEME.spacing.sm,
  },
  sectionHeaderRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginTop: THEME.spacing.md,
    marginBottom: THEME.spacing.sm,
  },
  seeAllText: {
    fontSize: THEME.typography.sizes.xs,
    fontWeight: THEME.typography.weights.semibold,
    color: THEME.colors.primary,
  },
  statsGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: THEME.spacing.sm,
    marginBottom: THEME.spacing.lg,
  },
  gridItem: {
    width: '48%',
    flexGrow: 1,
  },
  quickActionRow: {
    flexDirection: 'row',
    gap: THEME.spacing.sm,
    marginBottom: THEME.spacing.lg,
  },
  actionBtn: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: THEME.spacing.md,
    borderRadius: THEME.borderRadius.md,
    gap: 6,
  },
  actionBtnPrimary: {
    backgroundColor: THEME.colors.primary,
    ...THEME.shadow.sm,
  },
  actionBtnSecondary: {
    backgroundColor: THEME.colors.surface,
    borderWidth: 1,
    borderColor: THEME.colors.primary + '55',
  },
  actionBtnTextPrimary: {
    fontSize: THEME.typography.sizes.xs,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.surface,
  },
  actionBtnTextSecondary: {
    fontSize: THEME.typography.sizes.xs,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.primary,
  },
  sesiCard: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: THEME.colors.surface,
    padding: THEME.spacing.md,
    borderRadius: THEME.borderRadius.lg,
    borderWidth: 1,
    borderColor: THEME.colors.border,
    marginBottom: THEME.spacing.sm,
    ...THEME.shadow.sm,
  },
  sesiCardLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
    marginRight: THEME.spacing.sm,
  },
  calendarBadge: {
    width: 44,
    height: 48,
    borderRadius: THEME.borderRadius.md,
    backgroundColor: THEME.colors.surfaceVariant,
    borderWidth: 1,
    borderColor: THEME.colors.border,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: THEME.spacing.md,
  },
  calendarBadgeToday: {
    backgroundColor: THEME.colors.primaryMuted,
    borderColor: THEME.colors.primary + '55',
  },
  calendarBadgeDay: {
    fontSize: THEME.typography.sizes.md,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.text,
    lineHeight: 18,
  },
  calendarBadgeDayToday: {
    color: THEME.colors.primary,
  },
  calendarBadgeMonth: {
    fontSize: 9,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.textMuted,
    marginTop: 1,
  },
  calendarBadgeMonthToday: {
    color: THEME.colors.primary,
  },
  sesiCardInfo: {
    flex: 1,
  },
  sesiCardTitle: {
    fontSize: THEME.typography.sizes.sm,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.text,
  },
  sesiCardSub: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textSecondary,
    marginTop: 2,
  },
  sesiCardPeserta: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textMuted,
    marginTop: 2,
  },
  sesiCardRight: {
    alignItems: 'flex-end',
  },
  statusPill: {
    paddingHorizontal: THEME.spacing.sm,
    paddingVertical: 3,
    borderRadius: THEME.borderRadius.sm,
  },
  statusText: {
    fontSize: 10,
    fontWeight: THEME.typography.weights.semibold,
  },
  emptyCard: {
    backgroundColor: THEME.colors.surface,
    borderRadius: THEME.borderRadius.lg,
    padding: THEME.spacing.xl,
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 1,
    borderColor: THEME.colors.border,
  },
  emptyText: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textMuted,
    marginTop: THEME.spacing.sm,
  },
  leaderboardCard: {
    backgroundColor: THEME.colors.surface,
    borderRadius: THEME.borderRadius.lg,
    padding: THEME.spacing.md,
    borderWidth: 1,
    borderColor: THEME.colors.border,
    ...THEME.shadow.sm,
  },
  leaderboardRow: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: THEME.spacing.sm,
  },
  leaderboardDivider: {
    borderTopWidth: 1,
    borderTopColor: THEME.colors.surfaceVariant,
  },
  rankCircle: {
    width: 28,
    height: 28,
    borderRadius: THEME.borderRadius.full,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: THEME.spacing.md,
  },
  rank1: {
    backgroundColor: THEME.colors.badgeGold,
  },
  rank2: {
    backgroundColor: '#94A3B8',
  },
  rank3: {
    backgroundColor: THEME.colors.badgeBronze,
  },
  rankDefault: {
    backgroundColor: THEME.colors.surfaceVariant,
  },
  rankText: {
    fontSize: THEME.typography.sizes.xs,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.textSecondary,
  },
  rankTextTop: {
    color: THEME.colors.surface,
  },
  leaderboardInfo: {
    flex: 1,
  },
  leaderboardName: {
    fontSize: THEME.typography.sizes.sm,
    fontWeight: THEME.typography.weights.semibold,
    color: THEME.colors.text,
  },
  leaderboardSchool: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textMuted,
  },
  leaderboardScoreBox: {
    alignItems: 'flex-end',
  },
  leaderboardScore: {
    fontSize: THEME.typography.sizes.md,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.primary,
  },
  leaderboardScoreLabel: {
    fontSize: 9,
    color: THEME.colors.textMuted,
  },
});
