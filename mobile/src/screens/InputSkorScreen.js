import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  TextInput,
  Alert,
  ActivityIndicator,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { THEME } from '../constants/theme';
import { ApiService } from '../services/api';
import { useAuth } from '../context/AuthContext';

const KEYPAD_BUTTONS = [
  { label: 'X', value: 10, isX: true, bg: '#FEF08A', text: '#854D0E' },
  { label: '10', value: 10, bg: '#FEF08A', text: '#854D0E' },
  { label: '9', value: 9, bg: '#FEF08A', text: '#854D0E' },
  { label: '8', value: 8, bg: '#FEE2E2', text: '#991B1B' },
  { label: '7', value: 7, bg: '#FEE2E2', text: '#991B1B' },
  { label: '6', value: 6, bg: '#DBEAFE', text: '#1E40AF' },
  { label: '5', value: 5, bg: '#DBEAFE', text: '#1E40AF' },
  { label: '4', value: 4, bg: '#F3F4F6', text: '#1F2937' },
  { label: '3', value: 3, bg: '#F3F4F6', text: '#1F2937' },
  { label: '2', value: 2, bg: '#FFFFFF', text: '#111827', border: true },
  { label: '1', value: 1, bg: '#FFFFFF', text: '#111827', border: true },
  { label: 'M', value: 0, bg: '#E5E7EB', text: '#6B7280' },
];

export function InputSkorScreen({ navigation, route }) {
  const { role, atlet: currentAtlet } = useAuth();

  // Data Sources
  const [sessions, setSessions] = useState([]);
  const [athletes, setAthletes] = useState([]);
  const [loadingInitial, setLoadingInitial] = useState(true);
  const [submitting, setSubmitting] = useState(false);

  // Form State
  const [selectedSesiId, setSelectedSesiId] = useState(route?.params?.sesiLatihanId || null);
  const [selectedAtletIds, setSelectedAtletIds] = useState([]);
  const [activeAthleteId, setActiveAthleteId] = useState(null);
  const [jarakMeter, setJarakMeter] = useState(30);
  const [tanggalSesi, setTanggalSesi] = useState(() => new Date().toISOString().split('T')[0]);
  const [catatanPelatih, setCatatanPelatih] = useState('');

  // Skor Matrix: { [atletId]: [ [s1, s2, s3, s4, s5, s6], ... ] }
  const [scores, setScores] = useState({});
  const [currentEndIndex, setCurrentEndIndex] = useState(0);
  const [currentArrowIndex, setCurrentArrowIndex] = useState(0);

  // Draft state
  const [draftAvailable, setDraftAvailable] = useState(false);

  // Load Sesi & Atlet
  const loadMasterData = useCallback(async () => {
    try {
      const [sesiRes, atletRes] = await Promise.all([
        ApiService.get('/sesi-latihan'),
        ApiService.get('/atlet'),
      ]);

      const sesiList = Array.isArray(sesiRes?.data) ? sesiRes.data : (sesiRes?.data?.data || []);
      setSessions(sesiList);
      if (!selectedSesiId && sesiList.length > 0) {
        const activeSesi = sesiList.find((s) => s.status === 'berlangsung' || s.is_today);
        setSelectedSesiId(activeSesi ? activeSesi.id : sesiList[0].id);
      }

      const atletList = atletRes?.data || [];
      setAthletes(atletList);

      // Auto-select athlete jika user login adalah atlet
      if (role === 'atlet' && currentAtlet?.id) {
        setSelectedAtletIds([currentAtlet.id]);
        setActiveAthleteId(currentAtlet.id);
        initScoreForAthlete(currentAtlet.id);
      } else if (atletList.length > 0) {
        const defaultAtlet = atletList[0];
        setSelectedAtletIds([defaultAtlet.id]);
        setActiveAthleteId(defaultAtlet.id);
        initScoreForAthlete(defaultAtlet.id);
      }

      // Periksa keberadaan offline draft
      const draft = await ApiService.getOfflineDraft();
      if (draft && draft.scores) {
        setDraftAvailable(true);
      }
    } catch (err) {
      console.warn('Gagal memuat master data input skor:', err);
    } finally {
      setLoadingInitial(false);
    }
  }, [role, currentAtlet, selectedSesiId]);

  useEffect(() => {
    loadMasterData();
  }, [loadMasterData]);

  // Initial End setup: 6 ends, each 6 arrows with 0
  const initScoreForAthlete = (atletId) => {
    setScores((prev) => {
      if (prev[atletId]) return prev;
      const initialEnds = Array(6).fill(null).map(() => Array(6).fill(0));
      return {
        ...prev,
        [atletId]: initialEnds,
      };
    });
  };

  const toggleSelectAthlete = (id) => {
    if (role === 'atlet') return; // Atlet hanya bisa input untuk dirinya sendiri

    setSelectedAtletIds((prev) => {
      let next;
      if (prev.includes(id)) {
        if (prev.length === 1) return prev; // Minimal 1 atlet
        next = prev.filter((item) => item !== id);
      } else {
        next = [...prev, id];
      }

      if (!scores[id]) {
        initScoreForAthlete(id);
      }

      if (!next.includes(activeAthleteId)) {
        setActiveAthleteId(next[0] || null);
      }

      return next;
    });
  };

  const currentAthleteScores = (activeAthleteId && scores[activeAthleteId]) || [];
  const currentEnd = currentAthleteScores[currentEndIndex] || Array(6).fill(0);

  // Keypad press handler
  const handleKeypadPress = (val) => {
    if (!activeAthleteId) return;

    setScores((prev) => {
      const athleteEnds = prev[activeAthleteId] ? [...prev[activeAthleteId]] : Array(6).fill(null).map(() => Array(6).fill(0));
      const newEnd = [...(athleteEnds[currentEndIndex] || Array(6).fill(0))];
      newEnd[currentArrowIndex] = val;
      athleteEnds[currentEndIndex] = newEnd;

      return {
        ...prev,
        [activeAthleteId]: athleteEnds,
      };
    });

    // Pindah ke arrow berikutnya
    if (currentArrowIndex < 5) {
      setCurrentArrowIndex(currentArrowIndex + 1);
    }
  };

  const handleBackspace = () => {
    if (!activeAthleteId) return;

    setScores((prev) => {
      const athleteEnds = prev[activeAthleteId] ? [...prev[activeAthleteId]] : Array(6).fill(null).map(() => Array(6).fill(0));
      const newEnd = [...(athleteEnds[currentEndIndex] || Array(6).fill(0))];
      newEnd[currentArrowIndex] = 0;
      athleteEnds[currentEndIndex] = newEnd;

      return {
        ...prev,
        [activeAthleteId]: athleteEnds,
      };
    });

    if (currentArrowIndex > 0) {
      setCurrentArrowIndex(currentArrowIndex - 1);
    }
  };

  // Hitung total poin atlet aktif
  const calculateTotal = (atletId) => {
    const ends = scores[atletId] || [];
    return ends.reduce((acc, end) => {
      return acc + end.reduce((sum, arrow) => sum + arrow, 0);
    }, 0);
  };

  const calculateEndTotal = (end) => {
    return (end || []).reduce((sum, v) => sum + v, 0);
  };

  // Simpan Draft Offline
  const handleSaveDraft = async () => {
    try {
      await ApiService.saveOfflineDraft({
        sesi_latihan_id: selectedSesiId,
        tanggal_sesi: tanggalSesi,
        jarak_meter: jarakMeter,
        catatan_pelatih: catatanPelatih,
        atlet_ids: selectedAtletIds,
        scores: scores,
      });
      setDraftAvailable(true);
      Alert.alert('Draft Tersimpan', 'Skor berhasil disimpan di memori HP. Anda dapat memulihkannya kapan saja.');
    } catch {
      Alert.alert('Gagal', 'Tidak dapat menyimpan draft lokal.');
    }
  };

  // Pulihkan Draft
  const handleRestoreDraft = async () => {
    const draft = await ApiService.getOfflineDraft();
    if (draft) {
      if (draft.sesi_latihan_id) setSelectedSesiId(draft.sesi_latihan_id);
      if (draft.tanggal_sesi) setTanggalSesi(draft.tanggal_sesi);
      if (draft.jarak_meter) setJarakMeter(draft.jarak_meter);
      if (draft.catatan_pelatih) setCatatanPelatih(draft.catatan_pelatih);
      if (draft.atlet_ids?.length) {
        setSelectedAtletIds(draft.atlet_ids);
        setActiveAthleteId(draft.atlet_ids[0]);
      }
      if (draft.scores) setScores(draft.scores);
      Alert.alert('Draft Dipulihkan', 'Data draft lokal berhasil dimuat.');
    }
  };

  // Submit REST API
  const handleSubmit = async () => {
    if (!selectedSesiId) {
      Alert.alert('Perhatian', 'Pilih sesi latihan mingguan terlebih dahulu.');
      return;
    }
    if (selectedAtletIds.length === 0) {
      Alert.alert('Perhatian', 'Pilih minimal satu atlet.');
      return;
    }

    setSubmitting(true);
    try {
      // Filter skor HANYA untuk atlet yang sedang dipilih (selectedAtletIds)
      const cleanSkor = {};
      selectedAtletIds.forEach((id) => {
        const rawEnds = scores[id] || [Array(6).fill(0)];
        // Temukan end terakhir yang memiliki nilai panah (atau minimal End 1)
        let lastEndIdx = 0;
        rawEnds.forEach((end, idx) => {
          if (end && end.some((arrow) => arrow > 0)) {
            lastEndIdx = Math.max(lastEndIdx, idx);
          }
        });
        cleanSkor[id] = rawEnds.slice(0, lastEndIdx + 1);
      });

      // Format payload sesuai StoreSkorRequest
      const payload = {
        sesi_latihan_id: selectedSesiId,
        tanggal_sesi: tanggalSesi,
        jarak_meter: parseInt(jarakMeter, 10) || 30,
        catatan_pelatih: catatanPelatih || null,
        atlet_ids: selectedAtletIds.map(Number),
        skor: cleanSkor,
      };

      await ApiService.post('/skor', payload);

      // Bersihkan draft offline setelah berhasil upload
      await ApiService.clearOfflineDraft();
      setDraftAvailable(false);

      Alert.alert(
        'Berhasil!',
        'Data skor latihan mingguan berhasil disimpan ke sistem.',
        [
          {
            text: 'OK',
            onPress: () => navigation.navigate('SesiLatihan', { selectedId: selectedSesiId }),
          },
        ]
      );
    } catch (err) {
      Alert.alert('Gagal Menyimpan Skor', err.message || 'Terjadi kesalahan pada server.');
    } finally {
      setSubmitting(false);
    }
  };

  if (loadingInitial) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color={THEME.colors.primary} />
        <Text style={styles.loadingText}>Menyiapkan papan skor...</Text>
      </View>
    );
  }

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.contentContainer}>
      {/* Draft Notification */}
      {draftAvailable && (
        <View style={styles.draftBanner}>
          <Ionicons name="cloud-offline-outline" size={18} color={THEME.colors.primary} />
          <Text style={styles.draftText}>Terdapat draft skor tersimpan di perangkat ini.</Text>
          <TouchableOpacity onPress={handleRestoreDraft} style={styles.draftBtn}>
            <Text style={styles.draftBtnText}>Pulihkan</Text>
          </TouchableOpacity>
        </View>
      )}

      {/* Sesi Selector */}
      <View style={styles.formGroup}>
        <Text style={styles.formLabel}>Pilih Sesi Latihan</Text>
        <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.horizontalScroll}>
          {sessions.map((sesi) => (
            <TouchableOpacity
              key={sesi.id}
              style={[styles.sessionChip, selectedSesiId === sesi.id && styles.sessionChipActive]}
              onPress={() => setSelectedSesiId(sesi.id)}
              activeOpacity={0.7}
            >
              <Text style={[styles.sessionChipText, selectedSesiId === sesi.id && styles.sessionChipTextActive]}>
                {sesi.nama_sesi}
              </Text>
              <Text style={[styles.sessionChipDate, selectedSesiId === sesi.id && styles.sessionChipDateActive]}>
                {sesi.tanggal_formatted}
              </Text>
            </TouchableOpacity>
          ))}
        </ScrollView>
      </View>

      {/* Atlet Selector */}
      <View style={styles.formGroup}>
        <Text style={styles.formLabel}>
          Pilih Atlet {role === 'pelatih' && '(Bisa pilih lebih dari satu)'}
        </Text>
        <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.horizontalScroll}>
          {athletes.map((a) => {
            const isSelected = selectedAtletIds.includes(a.id);
            const isActive = activeAthleteId === a.id;
            return (
              <TouchableOpacity
                key={a.id}
                style={[
                  styles.atletChip,
                  isSelected && styles.atletChipSelected,
                  isActive && styles.atletChipActive,
                ]}
                onPress={() => {
                  toggleSelectAthlete(a.id);
                  setActiveAthleteId(a.id);
                }}
                activeOpacity={0.7}
              >
                <View style={styles.atletChipRow}>
                  {isSelected && <Ionicons name="checkmark-circle" size={14} color={isActive ? THEME.colors.surface : THEME.colors.primary} />}
                  <Text style={[styles.atletChipName, isActive && styles.textWhite]}>
                    {a.nama_lengkap}
                  </Text>
                </View>
                <Text style={[styles.atletChipSub, isActive && styles.textWhiteMuted]}>
                  {a.kategori || 'Umum'} • Target #{a.nomor_target || '-'}
                </Text>
              </TouchableOpacity>
            );
          })}
        </ScrollView>
      </View>

      {/* Target Setting (Jarak & Tanggal) */}
      <View style={styles.settingRow}>
        <View style={[styles.formGroup, { flex: 1, marginRight: THEME.spacing.sm }]}>
          <Text style={styles.formLabel}>Jarak Tembak (Meter)</Text>
          <View style={styles.inputWrap}>
            <TextInput
              style={styles.textInput}
              keyboardType="numeric"
              value={String(jarakMeter)}
              onChangeText={setJarakMeter}
            />
            <Text style={styles.inputUnit}>m</Text>
          </View>
        </View>

        <View style={[styles.formGroup, { flex: 1.3 }]}>
          <Text style={styles.formLabel}>Tanggal Sesi</Text>
          <View style={styles.inputWrap}>
            <TextInput
              style={styles.textInput}
              value={tanggalSesi}
              onChangeText={setTanggalSesi}
              placeholder="YYYY-MM-DD"
            />
            <Ionicons name="calendar-outline" size={16} color={THEME.colors.textMuted} />
          </View>
        </View>
      </View>

      {/* End Tabs (End 1..6) & Running Score Display */}
      {activeAthleteId && (
        <View style={styles.scoringBoard}>
          <View style={styles.scoringHeader}>
            <View>
              <Text style={styles.activeAthleteName}>
                {athletes.find((a) => a.id === activeAthleteId)?.nama_lengkap || 'Atlet Aktif'}
              </Text>
              <Text style={styles.runningTotalText}>
                Total Skor: <Text style={styles.runningTotalVal}>{calculateTotal(activeAthleteId)}</Text> / 360
              </Text>
            </View>
            <View style={styles.endSumBadge}>
              <Text style={styles.endSumLabel}>End {currentEndIndex + 1}</Text>
              <Text style={styles.endSumVal}>{calculateEndTotal(currentEnd)} / 60</Text>
            </View>
          </View>

          {/* End Tabs Selector */}
          <View style={styles.endTabsRow}>
            {[0, 1, 2, 3, 4, 5].map((idx) => {
              const endScore = calculateEndTotal(scores[activeAthleteId]?.[idx]);
              const isCurrentEnd = currentEndIndex === idx;
              return (
                <TouchableOpacity
                  key={idx}
                  style={[styles.endTab, isCurrentEnd && styles.endTabActive]}
                  onPress={() => {
                    setCurrentEndIndex(idx);
                    setCurrentArrowIndex(0);
                  }}
                  activeOpacity={0.7}
                >
                  <Text style={[styles.endTabText, isCurrentEnd && styles.endTabTextActive]}>
                    End {idx + 1}
                  </Text>
                  <Text style={[styles.endTabSub, isCurrentEnd && styles.endTabSubActive]}>
                    {endScore}
                  </Text>
                </TouchableOpacity>
              );
            })}
          </View>

          {/* 6 Arrows Slots */}
          <View style={styles.arrowSlotsRow}>
            {[0, 1, 2, 3, 4, 5].map((idx) => {
              const val = currentEnd[idx];
              const isSelectedSlot = currentArrowIndex === idx;
              return (
                <TouchableOpacity
                  key={idx}
                  style={[
                    styles.arrowSlot,
                    isSelectedSlot && styles.arrowSlotActive,
                    val === 10 && styles.slotGold,
                    val >= 7 && val <= 9 && styles.slotRed,
                    val >= 5 && val <= 6 && styles.slotBlue,
                  ]}
                  onPress={() => setCurrentArrowIndex(idx)}
                  activeOpacity={0.8}
                >
                  <Text style={styles.slotNumberLabel}>#{idx + 1}</Text>
                  <Text style={[styles.slotValue, val === 10 && styles.slotValueGold]}>
                    {val === 0 ? '-' : (val === 10 ? 'X' : val)}
                  </Text>
                </TouchableOpacity>
              );
            })}
          </View>

          {/* Keypad */}
          <View style={styles.keypadGrid}>
            {KEYPAD_BUTTONS.map((btn) => (
              <TouchableOpacity
                key={btn.label}
                style={[
                  styles.keypadBtn,
                  { backgroundColor: btn.bg },
                  btn.border && styles.keypadBtnBorder,
                ]}
                onPress={() => handleKeypadPress(btn.value)}
                activeOpacity={0.7}
              >
                <Text style={[styles.keypadBtnText, { color: btn.text }]}>{btn.label}</Text>
              </TouchableOpacity>
            ))}
            <TouchableOpacity
              style={[styles.keypadBtn, styles.backspaceBtn]}
              onPress={handleBackspace}
              activeOpacity={0.7}
            >
              <Ionicons name="backspace-outline" size={20} color={THEME.colors.textSecondary} />
            </TouchableOpacity>
          </View>
        </View>
      )}

      {/* Evaluasi / Catatan Pelatih */}
      <View style={styles.formGroup}>
        <Text style={styles.formLabel}>Catatan Evaluasi / Masukan</Text>
        <TextInput
          style={styles.textArea}
          multiline
          numberOfLines={3}
          value={catatanPelatih}
          onChangeText={setCatatanPelatih}
          placeholder="Tuliskan evaluasi teknik, stabilitas release, atau koreksi..."
          placeholderTextColor={THEME.colors.textMuted}
        />
      </View>

      {/* Action Buttons: Simpan Draft Offline & Submit REST API */}
      <View style={styles.actionRow}>
        <TouchableOpacity
          style={styles.draftSaveBtn}
          onPress={handleSaveDraft}
          activeOpacity={0.7}
        >
          <Ionicons name="save-outline" size={18} color={THEME.colors.textSecondary} />
          <Text style={styles.draftSaveBtnText}>Draft Lokal</Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={styles.submitBtn}
          onPress={handleSubmit}
          disabled={submitting}
          activeOpacity={0.8}
        >
          {submitting ? (
            <ActivityIndicator size="small" color={THEME.colors.surface} />
          ) : (
            <>
              <Ionicons name="cloud-upload-outline" size={18} color={THEME.colors.surface} />
              <Text style={styles.submitBtnText}>Simpan Skor Sesi</Text>
            </>
          )}
        </TouchableOpacity>
      </View>
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
    paddingBottom: 50,
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
  draftBanner: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: THEME.colors.primaryMuted,
    borderWidth: 1,
    borderColor: THEME.colors.primary + '33',
    padding: THEME.spacing.sm,
    borderRadius: THEME.borderRadius.md,
    marginBottom: THEME.spacing.md,
    gap: 8,
  },
  draftText: {
    flex: 1,
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.primaryDark,
  },
  draftBtn: {
    backgroundColor: THEME.colors.primary,
    paddingHorizontal: THEME.spacing.sm,
    paddingVertical: 4,
    borderRadius: THEME.borderRadius.sm,
  },
  draftBtnText: {
    fontSize: 10,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.surface,
  },
  formGroup: {
    marginBottom: THEME.spacing.md,
  },
  formLabel: {
    fontSize: THEME.typography.sizes.xs,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.textSecondary,
    marginBottom: THEME.spacing.xs,
    textTransform: 'uppercase',
    letterSpacing: 0.5,
  },
  horizontalScroll: {
    marginHorizontal: -THEME.spacing.lg,
    paddingHorizontal: THEME.spacing.lg,
  },
  sessionChip: {
    backgroundColor: THEME.colors.surface,
    paddingHorizontal: THEME.spacing.md,
    paddingVertical: THEME.spacing.sm,
    borderRadius: THEME.borderRadius.md,
    marginRight: THEME.spacing.sm,
    borderWidth: 1,
    borderColor: THEME.colors.border,
  },
  sessionChipActive: {
    backgroundColor: THEME.colors.primaryMuted,
    borderColor: THEME.colors.primary,
  },
  sessionChipText: {
    fontSize: THEME.typography.sizes.xs,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.text,
  },
  sessionChipTextActive: {
    color: THEME.colors.primary,
  },
  sessionChipDate: {
    fontSize: 10,
    color: THEME.colors.textMuted,
    marginTop: 2,
  },
  sessionChipDateActive: {
    color: THEME.colors.primaryDark,
  },
  atletChip: {
    backgroundColor: THEME.colors.surface,
    paddingHorizontal: THEME.spacing.md,
    paddingVertical: THEME.spacing.sm,
    borderRadius: THEME.borderRadius.md,
    marginRight: THEME.spacing.sm,
    borderWidth: 1,
    borderColor: THEME.colors.border,
  },
  atletChipSelected: {
    borderColor: THEME.colors.primary,
  },
  atletChipActive: {
    backgroundColor: THEME.colors.primary,
    borderColor: THEME.colors.primaryDark,
  },
  atletChipRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
  },
  atletChipName: {
    fontSize: THEME.typography.sizes.xs,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.text,
  },
  atletChipSub: {
    fontSize: 10,
    color: THEME.colors.textMuted,
    marginTop: 2,
  },
  textWhite: {
    color: THEME.colors.surface,
  },
  textWhiteMuted: {
    color: '#E8F5E9',
  },
  settingRow: {
    flexDirection: 'row',
  },
  inputWrap: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: THEME.colors.surface,
    borderRadius: THEME.borderRadius.md,
    borderWidth: 1,
    borderColor: THEME.colors.border,
    paddingHorizontal: THEME.spacing.md,
    height: 44,
  },
  textInput: {
    flex: 1,
    fontSize: THEME.typography.sizes.sm,
    color: THEME.colors.text,
  },
  inputUnit: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textMuted,
  },
  scoringBoard: {
    backgroundColor: THEME.colors.surface,
    borderRadius: THEME.borderRadius.lg,
    padding: THEME.spacing.md,
    borderWidth: 1,
    borderColor: THEME.colors.border,
    marginBottom: THEME.spacing.md,
    ...THEME.shadow.sm,
  },
  scoringHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: THEME.spacing.md,
    paddingBottom: THEME.spacing.sm,
    borderBottomWidth: 1,
    borderBottomColor: THEME.colors.surfaceVariant,
  },
  activeAthleteName: {
    fontSize: THEME.typography.sizes.md,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.text,
  },
  runningTotalText: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textSecondary,
    marginTop: 2,
  },
  runningTotalVal: {
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.primary,
  },
  endSumBadge: {
    backgroundColor: THEME.colors.primaryMuted,
    paddingHorizontal: THEME.spacing.md,
    paddingVertical: THEME.spacing.xs,
    borderRadius: THEME.borderRadius.md,
    alignItems: 'center',
  },
  endSumLabel: {
    fontSize: 10,
    color: THEME.colors.primaryDark,
    fontWeight: THEME.typography.weights.semibold,
  },
  endSumVal: {
    fontSize: THEME.typography.sizes.sm,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.primary,
  },
  endTabsRow: {
    flexDirection: 'row',
    gap: 4,
    marginBottom: THEME.spacing.md,
  },
  endTab: {
    flex: 1,
    backgroundColor: THEME.colors.surfaceVariant,
    paddingVertical: 6,
    borderRadius: THEME.borderRadius.sm,
    alignItems: 'center',
  },
  endTabActive: {
    backgroundColor: THEME.colors.primary,
  },
  endTabText: {
    fontSize: 10,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.textSecondary,
  },
  endTabTextActive: {
    color: THEME.colors.surface,
  },
  endTabSub: {
    fontSize: 9,
    color: THEME.colors.textMuted,
    marginTop: 1,
  },
  endTabSubActive: {
    color: '#E8F5E9',
  },
  arrowSlotsRow: {
    flexDirection: 'row',
    gap: 6,
    marginBottom: THEME.spacing.md,
  },
  arrowSlot: {
    flex: 1,
    height: 52,
    backgroundColor: THEME.colors.surfaceVariant,
    borderRadius: THEME.borderRadius.md,
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 2,
    borderColor: 'transparent',
  },
  arrowSlotActive: {
    borderColor: THEME.colors.primary,
    backgroundColor: THEME.colors.surface,
  },
  slotGold: {
    backgroundColor: '#FEF08A',
  },
  slotRed: {
    backgroundColor: '#FEE2E2',
  },
  slotBlue: {
    backgroundColor: '#DBEAFE',
  },
  slotNumberLabel: {
    fontSize: 9,
    color: THEME.colors.textMuted,
  },
  slotValue: {
    fontSize: THEME.typography.sizes.lg,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.text,
  },
  slotValueGold: {
    color: '#854D0E',
  },
  keypadGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 6,
  },
  keypadBtn: {
    width: '23%',
    height: 44,
    borderRadius: THEME.borderRadius.md,
    alignItems: 'center',
    justifyContent: 'center',
  },
  keypadBtnBorder: {
    borderWidth: 1,
    borderColor: THEME.colors.border,
  },
  keypadBtnText: {
    fontSize: THEME.typography.sizes.md,
    fontWeight: THEME.typography.weights.bold,
  },
  backspaceBtn: {
    backgroundColor: THEME.colors.surfaceVariant,
  },
  textArea: {
    backgroundColor: THEME.colors.surface,
    borderRadius: THEME.borderRadius.md,
    borderWidth: 1,
    borderColor: THEME.colors.border,
    padding: THEME.spacing.md,
    fontSize: THEME.typography.sizes.sm,
    color: THEME.colors.text,
    textAlignVertical: 'top',
  },
  actionRow: {
    flexDirection: 'row',
    gap: THEME.spacing.sm,
    marginTop: THEME.spacing.sm,
  },
  draftSaveBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: THEME.colors.surface,
    borderWidth: 1,
    borderColor: THEME.colors.border,
    paddingVertical: THEME.spacing.md,
    paddingHorizontal: THEME.spacing.md,
    borderRadius: THEME.borderRadius.md,
    gap: 6,
  },
  draftSaveBtnText: {
    fontSize: THEME.typography.sizes.xs,
    fontWeight: THEME.typography.weights.semibold,
    color: THEME.colors.textSecondary,
  },
  submitBtn: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: THEME.colors.primary,
    paddingVertical: THEME.spacing.md,
    borderRadius: THEME.borderRadius.md,
    gap: 8,
    ...THEME.shadow.sm,
  },
  submitBtnText: {
    fontSize: THEME.typography.sizes.sm,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.surface,
  },
});
