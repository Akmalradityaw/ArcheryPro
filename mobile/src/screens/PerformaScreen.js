import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  FlatList,
  TouchableOpacity,
  RefreshControl,
  ActivityIndicator,
  TextInput,
  Modal,
  Alert,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { THEME } from '../constants/theme';
import { useAuth } from '../context/AuthContext';
import { ApiService } from '../services/api';
import { CardStat } from '../components/CardStat';
import { BadgeItem } from '../components/BadgeItem';

export function PerformaScreen({ navigation }) {
  const { user, role, atlet: authAtlet } = useAuth();

  // Tab State: 'ringkasan' | 'riwayat' | 'badges'
  const [activeTab, setActiveTab] = useState('ringkasan');

  // Pelatih can select athlete; Atlet is locked to their own ID
  const [athleteList, setAthleteList] = useState([]);
  const [selectedAtletId, setSelectedAtletId] = useState(authAtlet?.id || null);

  // Performa Data
  const [performa, setPerforma] = useState(null);
  const [loadingPerforma, setLoadingPerforma] = useState(true);

  // Riwayat Data (Strict 10 items per page)
  const [riwayatItems, setRiwayatItems] = useState([]);
  const [riwayatPage, setRiwayatPage] = useState(1);
  const [riwayatLastPage, setRiwayatLastPage] = useState(1);
  const [loadingRiwayat, setLoadingRiwayat] = useState(false);
  const [loadingMoreRiwayat, setLoadingMoreRiwayat] = useState(false);

  const [refreshing, setRefreshing] = useState(false);

  // Coach Note Edit Modal (For Coach)
  const [evalModalVisible, setEvalModalVisible] = useState(false);
  const [selectedSesiIdForNote, setSelectedSesiIdForNote] = useState(null);
  const [coachNoteText, setCoachNoteText] = useState('');
  const [savingNote, setSavingNote] = useState(false);

  // Load Athlete List (if coach)
  useEffect(() => {
    if (role === 'pelatih' || role === 'admin') {
      ApiService.get('/atlet')
        .then((res) => {
          const list = res?.data || [];
          setAthleteList(list);
          if (!selectedAtletId && list.length > 0) {
            setSelectedAtletId(list[0].id);
          }
        })
        .catch((err) => console.warn('Gagal memuat list atlet:', err));
    }
  }, [role, selectedAtletId]);

  // Load Performa for selected athlete
  const loadPerforma = useCallback(async () => {
    if (!selectedAtletId) return;
    setLoadingPerforma(true);
    try {
      const res = await ApiService.get(`/atlet/${selectedAtletId}/performa`);
      if (res?.data) {
        setPerforma(res.data);
      }
    } catch (err) {
      console.warn('Gagal memuat performa atlet:', err);
    } finally {
      setLoadingPerforma(false);
      setRefreshing(false);
    }
  }, [selectedAtletId]);

  // Load Riwayat Sesi (Paginasi 10 items)
  const loadRiwayat = useCallback(async (page = 1, isRefresh = false) => {
    if (!selectedAtletId) return;
    if (page === 1 && !isRefresh) {
      setLoadingRiwayat(true);
    }

    try {
      const res = await ApiService.get(`/atlet/${selectedAtletId}/riwayat`, { page });
      const dataList = Array.isArray(res?.data) ? res.data : (res?.data?.data || []);
      if (page === 1) {
        setRiwayatItems(dataList);
      } else {
        setRiwayatItems((prev) => [...prev, ...dataList]);
      }
      setRiwayatPage(res.meta?.current_page || page);
      setRiwayatLastPage(res.meta?.last_page || 1);
    } catch (err) {
      console.warn('Gagal memuat riwayat latihan:', err);
    } finally {
      setLoadingRiwayat(false);
      setLoadingMoreRiwayat(false);
    }
  }, [selectedAtletId]);

  useEffect(() => {
    if (selectedAtletId) {
      loadPerforma();
      loadRiwayat(1);
    }
  }, [selectedAtletId, loadPerforma, loadRiwayat]);

  const onRefresh = () => {
    setRefreshing(true);
    loadPerforma();
    loadRiwayat(1, true);
  };

  const loadMoreRiwayat = () => {
    if (riwayatPage < riwayatLastPage && !loadingMoreRiwayat && !loadingRiwayat) {
      setLoadingMoreRiwayat(true);
      loadRiwayat(riwayatPage + 1);
    }
  };

  // Mark coach note read
  const handleMarkNoteRead = async (sesiId) => {
    try {
      await ApiService.post(`/sesi/${sesiId}/baca-catatan`);
      loadPerforma();
    } catch (err) {
      console.warn('Gagal menandai catatan dibaca:', err);
    }
  };

  // Save Coach Note (Coach action)
  const handleSaveCoachNote = async () => {
    if (!selectedSesiIdForNote) return;
    setSavingNote(true);
    try {
      await ApiService.post(`/sesi/${selectedSesiIdForNote}/catatan`, {
        catatan_pelatih: coachNoteText,
      });
      setEvalModalVisible(false);
      Alert.alert('Sukses', 'Catatan evaluasi berhasil diperbarui.');
      loadRiwayat(1);
      loadPerforma();
    } catch (err) {
      Alert.alert('Gagal', err.message || 'Tidak dapat memperbarui catatan.');
    } finally {
      setSavingNote(false);
    }
  };

  const openEditNote = (sesi) => {
    setSelectedSesiIdForNote(sesi.id);
    setCoachNoteText(sesi.catatan_pelatih || '');
    setEvalModalVisible(true);
  };

  return (
    <View style={styles.container}>
      {/* Athlete Selector for Coach */}
      {(role === 'pelatih' || role === 'admin') && athleteList.length > 0 && (
        <View style={styles.selectorBar}>
          <Text style={styles.selectorLabel}>Pilih Atlet:</Text>
          <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.athleteScroll}>
            {athleteList.map((a) => (
              <TouchableOpacity
                key={a.id}
                style={[styles.athleteChip, selectedAtletId === a.id && styles.athleteChipActive]}
                onPress={() => setSelectedAtletId(a.id)}
                activeOpacity={0.7}
              >
                <Text style={[styles.athleteChipText, selectedAtletId === a.id && styles.athleteChipTextActive]}>
                  {a.nama_lengkap}
                </Text>
              </TouchableOpacity>
            ))}
          </ScrollView>
        </View>
      )}

      {/* Tabs */}
      <View style={styles.tabsRow}>
        <TouchableOpacity
          style={[styles.tabBtn, activeTab === 'ringkasan' && styles.tabBtnActive]}
          onPress={() => setActiveTab('ringkasan')}
          activeOpacity={0.7}
        >
          <Ionicons
            name="analytics-outline"
            size={16}
            color={activeTab === 'ringkasan' ? THEME.colors.primary : THEME.colors.textSecondary}
          />
          <Text style={[styles.tabText, activeTab === 'ringkasan' && styles.tabTextActive]}>
            Ringkasan
          </Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={[styles.tabBtn, activeTab === 'riwayat' && styles.tabBtnActive]}
          onPress={() => setActiveTab('riwayat')}
          activeOpacity={0.7}
        >
          <Ionicons
            name="time-outline"
            size={16}
            color={activeTab === 'riwayat' ? THEME.colors.primary : THEME.colors.textSecondary}
          />
          <Text style={[styles.tabText, activeTab === 'riwayat' && styles.tabTextActive]}>
            Riwayat Sesi
          </Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={[styles.tabBtn, activeTab === 'badges' && styles.tabBtnActive]}
          onPress={() => setActiveTab('badges')}
          activeOpacity={0.7}
        >
          <Ionicons
            name="ribbon-outline"
            size={16}
            color={activeTab === 'badges' ? THEME.colors.primary : THEME.colors.textSecondary}
          />
          <Text style={[styles.tabText, activeTab === 'badges' && styles.tabTextActive]}>
            Badges
          </Text>
        </TouchableOpacity>
      </View>

      {/* Content */}
      {loadingPerforma && !performa ? (
        <View style={styles.centerContainer}>
          <ActivityIndicator size="large" color={THEME.colors.primary} />
          <Text style={styles.loadingText}>Memuat statistik performa atlet...</Text>
        </View>
      ) : activeTab === 'ringkasan' ? (
        <ScrollView
          style={styles.tabContent}
          contentContainerStyle={styles.scrollPadding}
          refreshControl={
            <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[THEME.colors.primary]} />
          }
        >
          {/* Unread Coach Note Banner */}
          {performa?.catatan_pelatih_baru && (
            <View style={styles.unreadNoteCard}>
              <View style={styles.noteTop}>
                <View style={styles.noteTitleWrap}>
                  <Ionicons name="chatbubble-ellipses" size={16} color={THEME.colors.warning} />
                  <Text style={styles.noteTitle}>Catatan Evaluasi Baru</Text>
                </View>
                {role === 'atlet' && (
                  <TouchableOpacity
                    style={styles.bacaBtn}
                    onPress={() => handleMarkNoteRead(performa.catatan_pelatih_baru.sesi_id)}
                  >
                    <Text style={styles.bacaBtnText}>Tandai Dibaca</Text>
                  </TouchableOpacity>
                )}
              </View>
              <Text style={styles.noteSesiInfo}>
                {performa.catatan_pelatih_baru.sesi_nama} • {performa.catatan_pelatih_baru.tanggal}
              </Text>
              <Text style={styles.noteBody}>"{performa.catatan_pelatih_baru.catatan}"</Text>
            </View>
          )}

          {/* Quick Stats Grid */}
          <View style={styles.statsGrid}>
            <View style={styles.gridItem}>
              <CardStat
                title="Total Sesi"
                value={performa?.statistik?.total_sesi ?? 0}
                subtitle="Sesi latihan"
                icon="calendar-outline"
                iconColor={THEME.colors.primary}
              />
            </View>
            <View style={styles.gridItem}>
              <CardStat
                title="Rata-rata Skor"
                value={performa?.statistik?.rata_rata_skor ?? 0}
                subtitle="Poin per sesi"
                icon="trending-up-outline"
                iconColor={THEME.colors.primary}
              />
            </View>
            <View style={styles.gridItem}>
              <CardStat
                title="Skor Terbaik"
                value={performa?.statistik?.skor_tertinggi ?? 0}
                subtitle="Rekor tertinggi"
                icon="trophy-outline"
                iconColor={THEME.colors.badgeGold}
              />
            </View>
            <View style={styles.gridItem}>
              <CardStat
                title="Total Anak Panah"
                value={performa?.statistik?.total_panah ?? 0}
                subtitle="Panah dilepaskan"
                icon="disc-outline"
                iconColor={THEME.colors.info}
              />
            </View>
          </View>

          {/* Stabilitas Release & Konsistensi */}
          <Text style={styles.sectionHeading}>Stabilitas & Konsistensi Tembakan</Text>
          <View style={styles.stabilityCard}>
            {performa?.stabilitas ? (
              <>
                <View style={styles.stabilityTop}>
                  <View>
                    <Text style={styles.stabilityLabel}>Kategori Konsistensi:</Text>
                    <Text style={styles.stabilityRating}>
                      {performa.stabilitas.kategori_stabilitas}
                    </Text>
                  </View>
                  <View style={styles.sdBadge}>
                    <Text style={styles.sdValue}>±{performa.stabilitas.standar_deviasi}</Text>
                    <Text style={styles.sdUnit}>Standar Deviasi</Text>
                  </View>
                </View>
                <View style={styles.stabilityBarWrap}>
                  <View
                    style={[
                      styles.stabilityBarFill,
                      {
                        width:
                          performa.stabilitas.standar_deviasi <= 2.2
                            ? '90%'
                            : (performa.stabilitas.standar_deviasi <= 4.0 ? '65%' : '35%'),
                        backgroundColor:
                          performa.stabilitas.standar_deviasi <= 2.2
                            ? THEME.colors.success
                            : (performa.stabilitas.standar_deviasi <= 4.0 ? THEME.colors.info : THEME.colors.warning),
                      },
                    ]}
                  />
                </View>
                <Text style={styles.stabilityHelp}>
                  Rata-rata {performa.stabilitas.rata_rata_per_end} poin/end dihitung dari {performa.stabilitas.jumlah_end} end pada sesi terakhir. Semakin kecil deviasi, semakin konsisten hasil tembakan.
                </Text>
              </>
            ) : (
              <Text style={styles.emptyNote}>
                Dibutuhkan minimal 2 end pada satu sesi latihan untuk menghitung indeks konsistensi.
              </Text>
            )}
          </View>

          {/* Moving Average Trend */}
          <Text style={styles.sectionHeading}>Tren Moving Average (4 Sesi Bergerak)</Text>
          {performa?.moving_average && performa.moving_average.length > 0 ? (
            <View style={styles.trendList}>
              {performa.moving_average.map((item, idx) => (
                <View key={idx} style={styles.trendItem}>
                  <View style={styles.trendLeft}>
                    <Text style={styles.trendTitle} numberOfLines={1}>{item.sesi_nama}</Text>
                    <Text style={styles.trendDate}>{item.tanggal || '-'}</Text>
                  </View>
                  <View style={styles.trendRight}>
                    <View style={styles.trendPillActual}>
                      <Text style={styles.trendLabel}>Aktual</Text>
                      <Text style={styles.trendValActual}>{item.skor_aktual}</Text>
                    </View>
                    <View style={styles.trendPillMA}>
                      <Text style={styles.trendLabel}>Rata-rata</Text>
                      <Text style={styles.trendValMA}>{item.moving_average}</Text>
                    </View>
                  </View>
                </View>
              ))}
            </View>
          ) : (
            <View style={styles.emptyCard}>
              <Text style={styles.emptyNote}>Belum ada data tren sesi yang cukup.</Text>
            </View>
          )}
        </ScrollView>
      ) : activeTab === 'riwayat' ? (
        <View style={styles.tabContent}>
          {loadingRiwayat && riwayatItems.length === 0 ? (
            <View style={styles.centerContainer}>
              <ActivityIndicator size="large" color={THEME.colors.primary} />
              <Text style={styles.loadingText}>Memuat riwayat latihan...</Text>
            </View>
          ) : riwayatItems.length === 0 ? (
            <View style={styles.centerContainer}>
              <Ionicons name="folder-open-outline" size={48} color={THEME.colors.textMuted} />
              <Text style={styles.emptyTitle}>Belum Ada Riwayat Sesi</Text>
            </View>
          ) : (
            <FlatList
              data={riwayatItems}
              keyExtractor={(item) => String(item.id)}
              contentContainerStyle={styles.scrollPadding}
              refreshControl={
                <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[THEME.colors.primary]} />
              }
              onEndReached={loadMoreRiwayat}
              onEndReachedThreshold={0.2}
              renderItem={({ item }) => (
                <View style={styles.riwayatCard}>
                  <View style={styles.riwayatHeader}>
                    <View style={{ flex: 1 }}>
                      <Text style={styles.riwayatTitle}>{item.nama_sesi}</Text>
                      <Text style={styles.riwayatDate}>{item.tanggal_formatted} • {item.jarak_meter || 30}m</Text>
                    </View>
                    <View style={styles.riwayatScoreBox}>
                      <Text style={styles.riwayatScoreVal}>{item.total_skor}</Text>
                      <Text style={styles.riwayatScoreLbl}>{item.total_end || 0} End</Text>
                    </View>
                  </View>

                  {/* Evaluasi Pelatih */}
                  {item.catatan_pelatih ? (
                    <View style={styles.riwayatNoteBox}>
                      <Text style={styles.riwayatNoteAuthor}>Catatan Evaluasi Pelatih:</Text>
                      <Text style={styles.riwayatNoteBody}>"{item.catatan_pelatih}"</Text>
                      {item.catatan_dibaca && (
                        <Text style={styles.riwayatReadInfo}>
                          <Ionicons name="checkmark-done" size={12} color={THEME.colors.primary} /> Sudah dibaca {item.catatan_dibaca_at || ''}
                        </Text>
                      )}
                    </View>
                  ) : null}

                  {/* Coach Action: Edit Note */}
                  {(role === 'pelatih' || role === 'admin') && (
                    <TouchableOpacity
                      style={styles.editNoteBtn}
                      onPress={() => openEditNote(item)}
                      activeOpacity={0.7}
                    >
                      <Ionicons name="create-outline" size={14} color={THEME.colors.primary} />
                      <Text style={styles.editNoteText}>
                        {item.catatan_pelatih ? 'Ubah Catatan Evaluasi' : '+ Berikan Catatan Evaluasi'}
                      </Text>
                    </TouchableOpacity>
                  )}
                </View>
              )}
              ListFooterComponent={
                loadingMoreRiwayat ? (
                  <View style={styles.footerLoader}>
                    <ActivityIndicator size="small" color={THEME.colors.primary} />
                    <Text style={styles.footerText}>Memuat 10 riwayat berikutnya...</Text>
                  </View>
                ) : riwayatPage >= riwayatLastPage && riwayatItems.length > 0 ? (
                  <View style={styles.footerLoader}>
                    <Text style={styles.footerText}>Menampilkan semua {riwayatItems.length} sesi</Text>
                  </View>
                ) : null
              }
            />
          )}
        </View>
      ) : (
        /* Badges Tab */
        <ScrollView
          style={styles.tabContent}
          contentContainerStyle={styles.scrollPadding}
          refreshControl={
            <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[THEME.colors.primary]} />
          }
        >
          <Text style={styles.sectionHeading}>Pencapaian & Badges Motivasi</Text>
          <Text style={styles.badgesSub}>
            Lencana prestasi yang didapatkan dari akumulasi kehadiran, volume tembakan, dan akurasi skor.
          </Text>

          {performa?.badges && performa.badges.length > 0 ? (
            performa.badges.map((b, idx) => <BadgeItem key={idx} badge={b} />)
          ) : (
            <View style={styles.emptyCard}>
              <Text style={styles.emptyNote}>Belum ada badge yang tersedia.</Text>
            </View>
          )}
        </ScrollView>
      )}

      {/* Edit Note Modal for Coach */}
      <Modal
        visible={evalModalVisible}
        transparent
        animationType="fade"
        onRequestClose={() => setEvalModalVisible(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalCard}>
            <Text style={styles.modalHeading}>Catatan Evaluasi Pelatih</Text>
            <TextInput
              style={styles.modalInput}
              multiline
              numberOfLines={4}
              value={coachNoteText}
              onChangeText={setCoachNoteText}
              placeholder="Berikan masukan koreksi form, stabilitas anchor, ritme rilis..."
              placeholderTextColor={THEME.colors.textMuted}
            />
            <View style={styles.modalButtons}>
              <TouchableOpacity
                style={styles.modalCancelBtn}
                onPress={() => setEvalModalVisible(false)}
              >
                <Text style={styles.modalCancelText}>Batal</Text>
              </TouchableOpacity>
              <TouchableOpacity
                style={styles.modalSaveBtn}
                onPress={handleSaveCoachNote}
                disabled={savingNote}
              >
                {savingNote ? (
                  <ActivityIndicator size="small" color={THEME.colors.surface} />
                ) : (
                  <Text style={styles.modalSaveText}>Simpan Catatan</Text>
                )}
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: THEME.colors.background,
  },
  selectorBar: {
    backgroundColor: THEME.colors.surface,
    paddingHorizontal: THEME.spacing.lg,
    paddingVertical: THEME.spacing.sm,
    borderBottomWidth: 1,
    borderBottomColor: THEME.colors.border,
  },
  selectorLabel: {
    fontSize: 10,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.textMuted,
    textTransform: 'uppercase',
    marginBottom: 4,
  },
  athleteScroll: {
    flexDirection: 'row',
  },
  athleteChip: {
    paddingHorizontal: THEME.spacing.md,
    paddingVertical: 6,
    borderRadius: THEME.borderRadius.full,
    backgroundColor: THEME.colors.surfaceVariant,
    marginRight: THEME.spacing.xs,
    borderWidth: 1,
    borderColor: THEME.colors.border,
  },
  athleteChipActive: {
    backgroundColor: THEME.colors.primary,
    borderColor: THEME.colors.primaryDark,
  },
  athleteChipText: {
    fontSize: THEME.typography.sizes.xs,
    fontWeight: THEME.typography.weights.semibold,
    color: THEME.colors.textSecondary,
  },
  athleteChipTextActive: {
    color: THEME.colors.surface,
  },
  tabsRow: {
    flexDirection: 'row',
    backgroundColor: THEME.colors.surface,
    borderBottomWidth: 1,
    borderBottomColor: THEME.colors.border,
  },
  tabBtn: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: THEME.spacing.md,
    gap: 6,
    borderBottomWidth: 2,
    borderBottomColor: 'transparent',
  },
  tabBtnActive: {
    borderBottomColor: THEME.colors.primary,
  },
  tabText: {
    fontSize: THEME.typography.sizes.xs,
    fontWeight: THEME.typography.weights.semibold,
    color: THEME.colors.textSecondary,
  },
  tabTextActive: {
    color: THEME.colors.primary,
    fontWeight: THEME.typography.weights.bold,
  },
  tabContent: {
    flex: 1,
  },
  scrollPadding: {
    padding: THEME.spacing.lg,
    paddingBottom: 40,
  },
  centerContainer: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    padding: THEME.spacing.xl,
  },
  loadingText: {
    fontSize: THEME.typography.sizes.sm,
    color: THEME.colors.textSecondary,
    marginTop: THEME.spacing.sm,
  },
  unreadNoteCard: {
    backgroundColor: THEME.colors.warningMuted,
    borderWidth: 1,
    borderColor: THEME.colors.warning + '55',
    borderRadius: THEME.borderRadius.md,
    padding: THEME.spacing.md,
    marginBottom: THEME.spacing.lg,
  },
  noteTop: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 4,
  },
  noteTitleWrap: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  noteTitle: {
    fontSize: THEME.typography.sizes.xs,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.warning,
  },
  bacaBtn: {
    backgroundColor: THEME.colors.surface,
    paddingHorizontal: THEME.spacing.sm,
    paddingVertical: 3,
    borderRadius: THEME.borderRadius.sm,
    borderWidth: 1,
    borderColor: THEME.colors.warning + '55',
  },
  bacaBtnText: {
    fontSize: 10,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.warning,
  },
  noteSesiInfo: {
    fontSize: 10,
    color: THEME.colors.textSecondary,
    marginBottom: 4,
  },
  noteBody: {
    fontSize: THEME.typography.sizes.sm,
    color: THEME.colors.text,
    fontStyle: 'italic',
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
  sectionHeading: {
    fontSize: THEME.typography.sizes.md,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.text,
    marginBottom: THEME.spacing.xs,
  },
  badgesSub: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textSecondary,
    marginBottom: THEME.spacing.md,
  },
  stabilityCard: {
    backgroundColor: THEME.colors.surface,
    borderRadius: THEME.borderRadius.lg,
    padding: THEME.spacing.md,
    borderWidth: 1,
    borderColor: THEME.colors.border,
    marginBottom: THEME.spacing.lg,
    ...THEME.shadow.sm,
  },
  stabilityTop: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: THEME.spacing.sm,
  },
  stabilityLabel: {
    fontSize: 10,
    color: THEME.colors.textMuted,
  },
  stabilityRating: {
    fontSize: THEME.typography.sizes.lg,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.primary,
  },
  sdBadge: {
    alignItems: 'flex-end',
  },
  sdValue: {
    fontSize: THEME.typography.sizes.md,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.text,
  },
  sdUnit: {
    fontSize: 9,
    color: THEME.colors.textMuted,
  },
  stabilityBarWrap: {
    height: 8,
    backgroundColor: THEME.colors.surfaceVariant,
    borderRadius: THEME.borderRadius.full,
    overflow: 'hidden',
    marginBottom: THEME.spacing.sm,
  },
  stabilityBarFill: {
    height: '100%',
    borderRadius: THEME.borderRadius.full,
  },
  stabilityHelp: {
    fontSize: 10,
    color: THEME.colors.textSecondary,
    lineHeight: 14,
  },
  trendList: {
    gap: THEME.spacing.xs,
  },
  trendItem: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: THEME.colors.surface,
    borderRadius: THEME.borderRadius.md,
    padding: THEME.spacing.md,
    borderWidth: 1,
    borderColor: THEME.colors.border,
  },
  trendLeft: {
    flex: 1,
    marginRight: THEME.spacing.sm,
  },
  trendTitle: {
    fontSize: THEME.typography.sizes.xs,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.text,
  },
  trendDate: {
    fontSize: 10,
    color: THEME.colors.textMuted,
    marginTop: 2,
  },
  trendRight: {
    flexDirection: 'row',
    gap: THEME.spacing.xs,
  },
  trendPillActual: {
    alignItems: 'center',
    backgroundColor: THEME.colors.surfaceVariant,
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: THEME.borderRadius.sm,
  },
  trendPillMA: {
    alignItems: 'center',
    backgroundColor: THEME.colors.primaryMuted,
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: THEME.borderRadius.sm,
  },
  trendLabel: {
    fontSize: 8,
    color: THEME.colors.textMuted,
  },
  trendValActual: {
    fontSize: THEME.typography.sizes.xs,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.text,
  },
  trendValMA: {
    fontSize: THEME.typography.sizes.xs,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.primary,
  },
  emptyCard: {
    backgroundColor: THEME.colors.surface,
    borderRadius: THEME.borderRadius.md,
    padding: THEME.spacing.lg,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: THEME.colors.border,
  },
  emptyTitle: {
    fontSize: THEME.typography.sizes.md,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.text,
    marginTop: THEME.spacing.sm,
  },
  emptyNote: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textMuted,
    textAlign: 'center',
  },
  riwayatCard: {
    backgroundColor: THEME.colors.surface,
    borderRadius: THEME.borderRadius.lg,
    padding: THEME.spacing.md,
    borderWidth: 1,
    borderColor: THEME.colors.border,
    marginBottom: THEME.spacing.sm,
    ...THEME.shadow.sm,
  },
  riwayatHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
  },
  riwayatTitle: {
    fontSize: THEME.typography.sizes.sm,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.text,
  },
  riwayatDate: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textSecondary,
    marginTop: 2,
  },
  riwayatScoreBox: {
    alignItems: 'flex-end',
  },
  riwayatScoreVal: {
    fontSize: THEME.typography.sizes.xl,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.primary,
  },
  riwayatScoreLbl: {
    fontSize: 9,
    color: THEME.colors.textMuted,
  },
  riwayatNoteBox: {
    backgroundColor: THEME.colors.surfaceVariant,
    padding: THEME.spacing.sm,
    borderRadius: THEME.borderRadius.sm,
    marginTop: THEME.spacing.sm,
  },
  riwayatNoteAuthor: {
    fontSize: 9,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.textSecondary,
  },
  riwayatNoteBody: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.text,
    fontStyle: 'italic',
    marginTop: 2,
  },
  riwayatReadInfo: {
    fontSize: 9,
    color: THEME.colors.primary,
    marginTop: 4,
  },
  editNoteBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    marginTop: THEME.spacing.sm,
    alignSelf: 'flex-start',
  },
  editNoteText: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.primary,
    fontWeight: THEME.typography.weights.semibold,
  },
  footerLoader: {
    paddingVertical: THEME.spacing.md,
    alignItems: 'center',
  },
  footerText: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textMuted,
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'center',
    padding: THEME.spacing.lg,
  },
  modalCard: {
    backgroundColor: THEME.colors.surface,
    borderRadius: THEME.borderRadius.lg,
    padding: THEME.spacing.lg,
  },
  modalHeading: {
    fontSize: THEME.typography.sizes.md,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.text,
    marginBottom: THEME.spacing.md,
  },
  modalInput: {
    backgroundColor: THEME.colors.surfaceVariant,
    borderRadius: THEME.borderRadius.md,
    padding: THEME.spacing.md,
    textAlignVertical: 'top',
    fontSize: THEME.typography.sizes.sm,
    color: THEME.colors.text,
    marginBottom: THEME.spacing.md,
  },
  modalButtons: {
    flexDirection: 'row',
    justifyContent: 'flex-end',
    gap: THEME.spacing.sm,
  },
  modalCancelBtn: {
    paddingHorizontal: THEME.spacing.md,
    paddingVertical: THEME.spacing.sm,
    borderRadius: THEME.borderRadius.sm,
  },
  modalCancelText: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textSecondary,
  },
  modalSaveBtn: {
    backgroundColor: THEME.colors.primary,
    paddingHorizontal: THEME.spacing.lg,
    paddingVertical: THEME.spacing.sm,
    borderRadius: THEME.borderRadius.sm,
  },
  modalSaveText: {
    fontSize: THEME.typography.sizes.xs,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.surface,
  },
});
