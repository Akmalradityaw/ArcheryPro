import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  RefreshControl,
  ActivityIndicator,
  Modal,
  ScrollView,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { THEME } from '../constants/theme';
import { ApiService } from '../services/api';
import { useAuth } from '../context/AuthContext';

export function SesiLatihanScreen({ navigation, route }) {
  const { role } = useAuth();
  const [filterStatus, setFilterStatus] = useState('semua');
  const [items, setItems] = useState([]);
  const [page, setPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);
  const [loading, setLoading] = useState(true);
  const [loadingMore, setLoadingMore] = useState(false);
  const [refreshing, setRefreshing] = useState(false);
  const [errorMsg, setErrorMsg] = useState(null);

  // Detail Modal State
  const [detailModalVisible, setDetailModalVisible] = useState(false);
  const [selectedSesi, setSelectedSesi] = useState(null);
  const [loadingDetail, setLoadingDetail] = useState(false);

  const fetchSesi = useCallback(async (targetPage = 1, isRefresh = false) => {
    if (targetPage === 1 && !isRefresh) {
      setLoading(true);
    }
    setErrorMsg(null);

    try {
      const params = {
        page: targetPage,
      };
      if (filterStatus !== 'semua') {
        params.status = filterStatus;
      }

      const res = await ApiService.get('/sesi-latihan', params);
      const dataList = Array.isArray(res?.data) ? res.data : (res?.data?.data || []);
      if (targetPage === 1) {
        setItems(dataList);
      } else {
        setItems((prev) => [...prev, ...dataList]);
      }
      setPage(res.meta?.current_page || targetPage);
      setLastPage(res.meta?.last_page || 1);
    } catch (err) {
      setErrorMsg(err.message || 'Gagal memuat daftar sesi.');
    } finally {
      setLoading(false);
      setLoadingMore(false);
      setRefreshing(false);
    }
  }, [filterStatus]);

  useEffect(() => {
    fetchSesi(1);
  }, [fetchSesi]);

  // Handle route param if passed from dashboard
  useEffect(() => {
    if (route?.params?.selectedId) {
      openDetail(route.params.selectedId);
    }
  }, [route?.params?.selectedId]);

  const onRefresh = () => {
    setRefreshing(true);
    fetchSesi(1, true);
  };

  const loadMore = () => {
    if (page < lastPage && !loadingMore && !loading) {
      setLoadingMore(true);
      fetchSesi(page + 1);
    }
  };

  const openDetail = async (id) => {
    setDetailModalVisible(true);
    setLoadingDetail(true);
    setSelectedSesi(null);

    try {
      const res = await ApiService.get(`/sesi-latihan/${id}`);
      if (res?.data) {
        setSelectedSesi(res.data);
      }
    } catch (err) {
      console.warn('Gagal memuat detail sesi:', err);
    } finally {
      setLoadingDetail(false);
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

  const renderItem = ({ item }) => (
    <TouchableOpacity
      style={styles.card}
      activeOpacity={0.7}
      onPress={() => openDetail(item.id)}
    >
      <View style={styles.cardHeader}>
        <View style={styles.headerLeft}>
          <Text style={styles.title} numberOfLines={1}>{item.nama_sesi}</Text>
          <Text style={styles.subtitle}>
            {item.tanggal_formatted} {item.jam_mulai ? `• ${item.jam_mulai} WIB` : ''}
          </Text>
        </View>
        {getStatusBadge(item.status)}
      </View>

      <View style={styles.cardBody}>
        <View style={styles.infoRow}>
          <Ionicons name="location-outline" size={14} color={THEME.colors.textSecondary} />
          <Text style={styles.infoText} numberOfLines={1}>{item.lokasi || 'Lapangan Panahan'}</Text>
        </View>
        <View style={styles.infoRow}>
          <Ionicons name="flag-outline" size={14} color={THEME.colors.textSecondary} />
          <Text style={styles.infoText} numberOfLines={1}>{item.fokus_latihan || 'Latihan Rutin'}</Text>
        </View>
      </View>

      <View style={styles.cardFooter}>
        <View style={styles.pesertaCount}>
          <Ionicons name="people" size={14} color={THEME.colors.primary} />
          <Text style={styles.pesertaText}>{item.total_peserta || 0} Atlet Terdaftar</Text>
        </View>
        <View style={styles.detailLink}>
          <Text style={styles.detailLinkText}>Lihat Hasil</Text>
          <Ionicons name="chevron-forward" size={14} color={THEME.colors.primary} />
        </View>
      </View>
    </TouchableOpacity>
  );

  return (
    <View style={styles.container}>
      {/* Filter Tabs */}
      <View style={styles.filterContainer}>
        {['semua', 'berlangsung', 'terjadwal', 'selesai'].map((st) => (
          <TouchableOpacity
            key={st}
            style={[styles.filterTab, filterStatus === st && styles.filterTabActive]}
            onPress={() => setFilterStatus(st)}
            activeOpacity={0.7}
          >
            <Text style={[styles.filterTabText, filterStatus === st && styles.filterTabTextActive]}>
              {st.charAt(0).toUpperCase() + st.slice(1)}
            </Text>
          </TouchableOpacity>
        ))}
      </View>

      {/* Main List */}
      {loading ? (
        <View style={styles.centerContainer}>
          <ActivityIndicator size="large" color={THEME.colors.primary} />
          <Text style={styles.loadingText}>Memuat jadwal sesi mingguan...</Text>
        </View>
      ) : errorMsg ? (
        <View style={styles.centerContainer}>
          <Ionicons name="alert-circle-outline" size={40} color={THEME.colors.error} />
          <Text style={styles.errorText}>{errorMsg}</Text>
          <TouchableOpacity style={styles.retryBtn} onPress={() => fetchSesi(1)}>
            <Text style={styles.retryText}>Coba Lagi</Text>
          </TouchableOpacity>
        </View>
      ) : items.length === 0 ? (
        <View style={styles.centerContainer}>
          <Ionicons name="calendar-outline" size={48} color={THEME.colors.textMuted} />
          <Text style={styles.emptyTitle}>Tidak Ada Sesi Ditemukan</Text>
          <Text style={styles.emptySubtitle}>Tidak ada jadwal sesi dengan filter saat ini.</Text>
        </View>
      ) : (
        <FlatList
          data={items}
          keyExtractor={(item) => String(item.id)}
          renderItem={renderItem}
          contentContainerStyle={styles.listContent}
          refreshControl={
            <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[THEME.colors.primary]} />
          }
          onEndReached={loadMore}
          onEndReachedThreshold={0.2}
          ListFooterComponent={
            loadingMore ? (
              <View style={styles.footerLoader}>
                <ActivityIndicator size="small" color={THEME.colors.primary} />
                <Text style={styles.footerText}>Memuat 10 data berikutnya...</Text>
              </View>
            ) : page >= lastPage && items.length > 0 ? (
              <View style={styles.footerLoader}>
                <Text style={styles.footerText}>Menampilkan semua {items.length} sesi</Text>
              </View>
            ) : null
          }
        />
      )}

      {/* Detail Sesi Modal */}
      <Modal
        visible={detailModalVisible}
        animationType="slide"
        presentationStyle="pageSheet"
        onRequestClose={() => setDetailModalVisible(false)}
      >
        <View style={styles.modalContainer}>
          <View style={styles.modalHeader}>
            <Text style={styles.modalTitle}>Detail & Skor Sesi</Text>
            <TouchableOpacity
              onPress={() => setDetailModalVisible(false)}
              style={styles.modalCloseBtn}
            >
              <Ionicons name="close" size={22} color={THEME.colors.text} />
            </TouchableOpacity>
          </View>

          {loadingDetail ? (
            <View style={styles.centerContainer}>
              <ActivityIndicator size="large" color={THEME.colors.primary} />
              <Text style={styles.loadingText}>Memuat hasil sesi...</Text>
            </View>
          ) : selectedSesi ? (
            <ScrollView contentContainerStyle={styles.modalBody}>
              {/* Sesi Info Box */}
              <View style={styles.detailBox}>
                <View style={styles.detailTitleRow}>
                  <Text style={styles.detailName}>{selectedSesi.nama_sesi}</Text>
                  {getStatusBadge(selectedSesi.status)}
                </View>
                <Text style={styles.detailDate}>
                  {selectedSesi.tanggal_formatted} {selectedSesi.jam_mulai ? `• ${selectedSesi.jam_mulai} - ${selectedSesi.jam_selesai || 'selesai'}` : ''}
                </Text>
                <Text style={styles.detailLocation}>
                  <Ionicons name="location-outline" size={13} color={THEME.colors.textSecondary} /> {selectedSesi.lokasi || 'Lapangan Panahan'}
                </Text>

                {/* Stat Sesi */}
                <View style={styles.statRow}>
                  <View style={styles.statBox}>
                    <Text style={styles.statVal}>{selectedSesi.statistik?.total_peserta ?? 0}</Text>
                    <Text style={styles.statLbl}>Peserta</Text>
                  </View>
                  <View style={styles.statBox}>
                    <Text style={styles.statVal}>{selectedSesi.statistik?.skor_tertinggi ?? 0}</Text>
                    <Text style={styles.statLbl}>Skor Tertinggi</Text>
                  </View>
                  <View style={styles.statBox}>
                    <Text style={styles.statVal}>{selectedSesi.statistik?.rata_rata_skor ?? 0}</Text>
                    <Text style={styles.statLbl}>Rata-rata</Text>
                  </View>
                </View>
              </View>

              {/* Action for Coach: Input Skor */}
              {(role === 'pelatih' || role === 'admin') && (
                <TouchableOpacity
                  style={styles.inputSkorBtn}
                  activeOpacity={0.8}
                  onPress={() => {
                    setDetailModalVisible(false);
                    navigation.navigate('InputSkor', { sesiLatihanId: selectedSesi.id });
                  }}
                >
                  <Ionicons name="create-outline" size={18} color={THEME.colors.surface} />
                  <Text style={styles.inputSkorBtnText}>Input Skor Atlet Untuk Sesi Ini</Text>
                </TouchableOpacity>
              )}

              {/* Leaderboard Atlet di Sesi Ini */}
              <Text style={styles.sectionHeading}>Peringkat Atlet ({selectedSesi.peserta?.length || 0})</Text>

              {(!selectedSesi.peserta || selectedSesi.peserta.length === 0) ? (
                <View style={styles.noPesertaBox}>
                  <Text style={styles.noPesertaText}>Belum ada skor atlet yang diinput untuk sesi ini.</Text>
                </View>
              ) : (
                selectedSesi.peserta.map((atletItem) => (
                  <View key={atletItem.sesi_id} style={styles.pesertaCard}>
                    <View style={styles.pesertaRankCircle}>
                      <Text style={styles.pesertaRankText}>{atletItem.peringkat}</Text>
                    </View>
                    <View style={styles.pesertaInfo}>
                      <Text style={styles.pesertaName} numberOfLines={1}>{atletItem.nama_lengkap}</Text>
                      <Text style={styles.pesertaSub} numberOfLines={1}>
                        {atletItem.sekolah || atletItem.kategori || '-'} • Jarak {atletItem.jarak_meter || 30}m
                      </Text>
                      {atletItem.catatan_pelatih && (
                        <Text style={styles.pesertaNote} numberOfLines={2}>
                          Eval: "{atletItem.catatan_pelatih}"
                        </Text>
                      )}
                    </View>
                    <View style={styles.pesertaScoreBox}>
                      <Text style={styles.pesertaScoreVal}>{atletItem.total_skor}</Text>
                      <Text style={styles.pesertaScoreLbl}>{atletItem.total_end || 0} End</Text>
                    </View>
                  </View>
                ))
              )}
            </ScrollView>
          ) : null}
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
  filterContainer: {
    flexDirection: 'row',
    backgroundColor: THEME.colors.surface,
    paddingHorizontal: THEME.spacing.lg,
    paddingVertical: THEME.spacing.sm,
    borderBottomWidth: 1,
    borderBottomColor: THEME.colors.border,
    gap: THEME.spacing.xs,
  },
  filterTab: {
    flex: 1,
    paddingVertical: THEME.spacing.sm,
    alignItems: 'center',
    borderRadius: THEME.borderRadius.sm,
  },
  filterTabActive: {
    backgroundColor: THEME.colors.primaryMuted,
  },
  filterTabText: {
    fontSize: THEME.typography.sizes.xs,
    fontWeight: THEME.typography.weights.medium,
    color: THEME.colors.textSecondary,
  },
  filterTabTextActive: {
    color: THEME.colors.primary,
    fontWeight: THEME.typography.weights.bold,
  },
  listContent: {
    padding: THEME.spacing.lg,
    gap: THEME.spacing.sm,
  },
  card: {
    backgroundColor: THEME.colors.surface,
    borderRadius: THEME.borderRadius.lg,
    padding: THEME.spacing.md,
    borderWidth: 1,
    borderColor: THEME.colors.border,
    ...THEME.shadow.sm,
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: THEME.spacing.xs,
  },
  headerLeft: {
    flex: 1,
    marginRight: THEME.spacing.sm,
  },
  title: {
    fontSize: THEME.typography.sizes.md,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.text,
  },
  subtitle: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textSecondary,
    marginTop: 2,
  },
  cardBody: {
    marginVertical: THEME.spacing.xs,
    gap: 4,
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  infoText: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textSecondary,
  },
  cardFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginTop: THEME.spacing.sm,
    paddingTop: THEME.spacing.xs,
    borderTopWidth: 1,
    borderTopColor: THEME.colors.surfaceVariant,
  },
  pesertaCount: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  pesertaText: {
    fontSize: THEME.typography.sizes.xs,
    fontWeight: THEME.typography.weights.semibold,
    color: THEME.colors.primary,
  },
  detailLink: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 2,
  },
  detailLinkText: {
    fontSize: THEME.typography.sizes.xs,
    fontWeight: THEME.typography.weights.semibold,
    color: THEME.colors.primary,
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
  emptyTitle: {
    fontSize: THEME.typography.sizes.md,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.text,
    marginTop: THEME.spacing.md,
  },
  emptySubtitle: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textMuted,
    marginTop: 4,
    textAlign: 'center',
  },
  errorText: {
    fontSize: THEME.typography.sizes.sm,
    color: THEME.colors.error,
    textAlign: 'center',
    marginTop: THEME.spacing.sm,
    marginBottom: THEME.spacing.md,
  },
  retryBtn: {
    backgroundColor: THEME.colors.primary,
    paddingHorizontal: THEME.spacing.lg,
    paddingVertical: THEME.spacing.sm,
    borderRadius: THEME.borderRadius.md,
  },
  retryText: {
    color: THEME.colors.surface,
    fontWeight: THEME.typography.weights.bold,
    fontSize: THEME.typography.sizes.xs,
  },
  footerLoader: {
    paddingVertical: THEME.spacing.md,
    alignItems: 'center',
  },
  footerText: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textMuted,
  },
  modalContainer: {
    flex: 1,
    backgroundColor: THEME.colors.background,
  },
  modalHeader: {
    height: 56,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: THEME.spacing.lg,
    backgroundColor: THEME.colors.surface,
    borderBottomWidth: 1,
    borderBottomColor: THEME.colors.border,
  },
  modalTitle: {
    fontSize: THEME.typography.sizes.md,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.text,
  },
  modalCloseBtn: {
    padding: THEME.spacing.xs,
  },
  modalBody: {
    padding: THEME.spacing.lg,
    paddingBottom: 40,
  },
  detailBox: {
    backgroundColor: THEME.colors.surface,
    borderRadius: THEME.borderRadius.lg,
    padding: THEME.spacing.lg,
    borderWidth: 1,
    borderColor: THEME.colors.border,
    marginBottom: THEME.spacing.md,
    ...THEME.shadow.sm,
  },
  detailTitleRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 4,
  },
  detailName: {
    flex: 1,
    fontSize: THEME.typography.sizes.lg,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.text,
    marginRight: THEME.spacing.sm,
  },
  detailDate: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textSecondary,
    marginBottom: 2,
  },
  detailLocation: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textSecondary,
    marginBottom: THEME.spacing.md,
  },
  statRow: {
    flexDirection: 'row',
    borderTopWidth: 1,
    borderTopColor: THEME.colors.surfaceVariant,
    paddingTop: THEME.spacing.md,
  },
  statBox: {
    flex: 1,
    alignItems: 'center',
  },
  statVal: {
    fontSize: THEME.typography.sizes.xl,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.primary,
  },
  statLbl: {
    fontSize: 10,
    color: THEME.colors.textMuted,
    textTransform: 'uppercase',
  },
  inputSkorBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: THEME.colors.primary,
    paddingVertical: THEME.spacing.md,
    borderRadius: THEME.borderRadius.md,
    gap: 8,
    marginBottom: THEME.spacing.lg,
    ...THEME.shadow.sm,
  },
  inputSkorBtnText: {
    color: THEME.colors.surface,
    fontSize: THEME.typography.sizes.sm,
    fontWeight: THEME.typography.weights.bold,
  },
  sectionHeading: {
    fontSize: THEME.typography.sizes.md,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.text,
    marginBottom: THEME.spacing.sm,
  },
  noPesertaBox: {
    padding: THEME.spacing.xl,
    backgroundColor: THEME.colors.surface,
    borderRadius: THEME.borderRadius.md,
    borderWidth: 1,
    borderColor: THEME.colors.border,
    alignItems: 'center',
  },
  noPesertaText: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textMuted,
  },
  pesertaCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: THEME.colors.surface,
    borderRadius: THEME.borderRadius.md,
    padding: THEME.spacing.md,
    marginBottom: THEME.spacing.xs,
    borderWidth: 1,
    borderColor: THEME.colors.border,
  },
  pesertaRankCircle: {
    width: 28,
    height: 28,
    borderRadius: THEME.borderRadius.full,
    backgroundColor: THEME.colors.primaryMuted,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: THEME.spacing.md,
  },
  pesertaRankText: {
    fontSize: THEME.typography.sizes.xs,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.primary,
  },
  pesertaInfo: {
    flex: 1,
    marginRight: THEME.spacing.sm,
  },
  pesertaName: {
    fontSize: THEME.typography.sizes.sm,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.text,
  },
  pesertaSub: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textSecondary,
  },
  pesertaNote: {
    fontSize: 10,
    color: THEME.colors.warning,
    fontStyle: 'italic',
    marginTop: 2,
  },
  pesertaScoreBox: {
    alignItems: 'flex-end',
  },
  pesertaScoreVal: {
    fontSize: THEME.typography.sizes.lg,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.primary,
  },
  pesertaScoreLbl: {
    fontSize: 9,
    color: THEME.colors.textMuted,
  },
});
