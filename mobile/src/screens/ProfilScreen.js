import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  Switch,
  Alert,
  TextInput,
  Modal,
  ActivityIndicator,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { THEME } from '../constants/theme';
import { useAuth } from '../context/AuthContext';
import { ApiService } from '../services/api';

export function ProfilScreen() {
  const { user, role, atlet, logout, refreshProfile } = useAuth();

  const [tampilPublik, setTampilPublik] = useState(Boolean(atlet?.izinkan_tampil_publik));
  const [updatingPrivasi, setUpdatingPrivasi] = useState(false);

  // Server IP Modal
  const [serverUrl, setServerUrl] = useState('');
  const [ipModalVisible, setIpModalVisible] = useState(false);
  const [testingConnection, setTestingConnection] = useState(false);
  const [connectionStatus, setConnectionStatus] = useState(null);

  useEffect(() => {
    ApiService.getBaseUrl().then((url) => setServerUrl(url));
    if (atlet) {
      setTampilPublik(Boolean(atlet.izinkan_tampil_publik));
    }
  }, [atlet]);

  const handleTogglePrivasi = async (value) => {
    setTampilPublik(value);
    setUpdatingPrivasi(true);
    try {
      await ApiService.put('/atlet/privasi', {
        izinkan_tampil_publik: value,
      });
      await refreshProfile();
    } catch (err) {
      setTampilPublik(!value); // revert
      Alert.alert('Gagal', err.message || 'Gagal memperbarui preferensi privasi.');
    } finally {
      setUpdatingPrivasi(false);
    }
  };

  const handleTestAndSaveServerUrl = async () => {
    setTestingConnection(true);
    setConnectionStatus(null);
    try {
      const cleanUrl = await ApiService.setBaseUrl(serverUrl);
      setServerUrl(cleanUrl);

      // Coba ping public leaderboard
      const res = await ApiService.get('/public/leaderboard');
      if (res) {
        setConnectionStatus({ success: true, message: 'Terhubung ke server ArcheryPro API!' });
      }
    } catch (err) {
      setConnectionStatus({ success: false, message: `Gagal terhubung: ${err.message}` });
    } finally {
      setTestingConnection(false);
    }
  };

  const handleLogout = () => {
    Alert.alert(
      'Konfirmasi Keluar',
      'Apakah Anda yakin ingin keluar dari akun ArcheryPro?',
      [
        { text: 'Batal', style: 'cancel' },
        {
          text: 'Keluar',
          style: 'destructive',
          onPress: async () => {
            await logout();
          },
        },
      ]
    );
  };

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.contentContainer}>
      {/* Profile Header Card */}
      <View style={styles.profileCard}>
        <View style={styles.avatarCircle}>
          <Text style={styles.avatarInitial}>
            {user?.nama_lengkap ? user.nama_lengkap.charAt(0).toUpperCase() : 'A'}
          </Text>
        </View>
        <Text style={styles.userName}>{user?.nama_lengkap || user?.username || 'Pengguna'}</Text>
        <View style={styles.rolePill}>
          <Text style={styles.rolePillText}>
            {role === 'pelatih' ? 'Pelatih Panahan' : (role === 'admin' ? 'Administrator' : 'Atlet Panahan')}
          </Text>
        </View>
        <Text style={styles.userEmail}>{user?.email || '-'}</Text>
      </View>

      {/* Info Atlet (Khusus role atlet) */}
      {role === 'atlet' && atlet && (
        <View style={styles.sectionCard}>
          <Text style={styles.sectionHeading}>Data Keanggotaan Atlet</Text>
          <View style={styles.infoRow}>
            <Text style={styles.infoLabel}>Nomor Induk Atlet (NIA)</Text>
            <Text style={styles.infoVal}>{atlet.nia || '-'}</Text>
          </View>
          <View style={styles.infoRow}>
            <Text style={styles.infoLabel}>Sekolah / Klub</Text>
            <Text style={styles.infoVal}>{atlet.sekolah?.nama_sekolah || '-'}</Text>
          </View>
          <View style={styles.infoRow}>
            <Text style={styles.infoLabel}>Kategori Latihan</Text>
            <Text style={styles.infoVal}>{atlet.kategori?.nama_kategori || '-'}</Text>
          </View>
          <View style={[styles.infoRow, { borderBottomWidth: 0 }]}>
            <Text style={styles.infoLabel}>Nomor Bantalan Target</Text>
            <Text style={styles.infoVal}>Target #{atlet.nomor_target || '-'}</Text>
          </View>
        </View>
      )}

      {/* Privasi Papan Skor (Khusus Atlet) */}
      {role === 'atlet' && (
        <View style={styles.sectionCard}>
          <Text style={styles.sectionHeading}>Pengaturan Privasi Skor</Text>
          <View style={styles.switchRow}>
            <View style={{ flex: 1, paddingRight: THEME.spacing.md }}>
              <Text style={styles.switchTitle}>Tampil di Papan Peringkat Publik</Text>
              <Text style={styles.switchSubtitle}>
                Izinkan skor latihan terbaik dan nama Anda ditampilkan pada tabel peringkat publik klub.
              </Text>
            </View>
            {updatingPrivasi ? (
              <ActivityIndicator size="small" color={THEME.colors.primary} />
            ) : (
              <Switch
                value={tampilPublik}
                onValueChange={handleTogglePrivasi}
                trackColor={{ false: THEME.colors.border, true: THEME.colors.primaryLight }}
                thumbColor={tampilPublik ? THEME.colors.primary : '#FFFFFF'}
              />
            )}
          </View>
        </View>
      )}

      {/* Sambungan Server & Jaringan */}
      <View style={styles.sectionCard}>
        <Text style={styles.sectionHeading}>Konfigurasi Server API</Text>
        <TouchableOpacity
          style={styles.serverRow}
          onPress={() => setIpModalVisible(true)}
          activeOpacity={0.7}
        >
          <View style={{ flex: 1 }}>
            <Text style={styles.serverLabel}>Alamat Backend REST API</Text>
            <Text style={styles.serverValue} numberOfLines={1}>{serverUrl || 'Belum diatur'}</Text>
          </View>
          <Ionicons name="settings-outline" size={18} color={THEME.colors.primary} />
        </TouchableOpacity>
      </View>

      {/* Informasi Aplikasi */}
      <View style={styles.sectionCard}>
        <Text style={styles.sectionHeading}>Tentang Aplikasi</Text>
        <View style={styles.infoRow}>
          <Text style={styles.infoLabel}>Aplikasi</Text>
          <Text style={styles.infoVal}>ArcheryPro Mobile</Text>
        </View>
        <View style={styles.infoRow}>
          <Text style={styles.infoLabel}>Versi</Text>
          <Text style={styles.infoVal}>1.0.0 (Expo SDK 57)</Text>
        </View>
        <View style={[styles.infoRow, { borderBottomWidth: 0 }]}>
          <Text style={styles.infoLabel}>Backend API</Text>
          <Text style={styles.infoVal}>Laravel 10 Sanctum REST API v1</Text>
        </View>
      </View>

      {/* Logout Button */}
      <TouchableOpacity
        style={styles.logoutBtn}
        onPress={handleLogout}
        activeOpacity={0.8}
      >
        <Ionicons name="log-out-outline" size={20} color={THEME.colors.error} />
        <Text style={styles.logoutBtnText}>Keluar dari Akun</Text>
      </TouchableOpacity>

      {/* Modal Ubah IP Server */}
      <Modal
        visible={ipModalVisible}
        transparent
        animationType="fade"
        onRequestClose={() => setIpModalVisible(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalCard}>
            <Text style={styles.modalHeading}>Pengaturan URL Server</Text>
            <Text style={styles.modalSub}>
              Gunakan IP jaringan WiFi laptop Anda saat testing di perangkat fisik HP Android/iOS (contoh: http://192.168.1.10:8000/api/v1).
            </Text>

            <TextInput
              style={styles.modalInput}
              value={serverUrl}
              onChangeText={setServerUrl}
              placeholder="http://192.168.x.x:8000/api/v1"
              autoCapitalize="none"
              autoCorrect={false}
            />

            {connectionStatus && (
              <View style={[
                styles.statusBox,
                connectionStatus.success ? styles.statusBoxSuccess : styles.statusBoxError
              ]}>
                <Ionicons
                  name={connectionStatus.success ? 'checkmark-circle' : 'alert-circle'}
                  size={16}
                  color={connectionStatus.success ? THEME.colors.success : THEME.colors.error}
                />
                <Text style={[
                  styles.statusBoxText,
                  connectionStatus.success ? styles.textSuccess : styles.textError
                ]}>
                  {connectionStatus.message}
                </Text>
              </View>
            )}

            <View style={styles.modalButtons}>
              <TouchableOpacity
                style={styles.modalCloseBtn}
                onPress={() => setIpModalVisible(false)}
              >
                <Text style={styles.modalCloseText}>Tutup</Text>
              </TouchableOpacity>
              <TouchableOpacity
                style={styles.modalSaveBtn}
                onPress={handleTestAndSaveServerUrl}
                disabled={testingConnection}
              >
                {testingConnection ? (
                  <ActivityIndicator size="small" color={THEME.colors.surface} />
                ) : (
                  <Text style={styles.modalSaveText}>Tes & Simpan</Text>
                )}
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
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
  profileCard: {
    backgroundColor: THEME.colors.surface,
    borderRadius: THEME.borderRadius.lg,
    padding: THEME.spacing.xl,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: THEME.colors.border,
    marginBottom: THEME.spacing.lg,
    ...THEME.shadow.sm,
  },
  avatarCircle: {
    width: 64,
    height: 64,
    borderRadius: THEME.borderRadius.full,
    backgroundColor: THEME.colors.primary,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: THEME.spacing.sm,
  },
  avatarInitial: {
    fontSize: THEME.typography.sizes.xxl,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.surface,
  },
  userName: {
    fontSize: THEME.typography.sizes.lg,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.text,
  },
  rolePill: {
    backgroundColor: THEME.colors.primaryMuted,
    paddingHorizontal: THEME.spacing.md,
    paddingVertical: 3,
    borderRadius: THEME.borderRadius.full,
    marginTop: 4,
    marginBottom: 6,
  },
  rolePillText: {
    fontSize: 10,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.primary,
    textTransform: 'uppercase',
  },
  userEmail: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textMuted,
  },
  sectionCard: {
    backgroundColor: THEME.colors.surface,
    borderRadius: THEME.borderRadius.lg,
    padding: THEME.spacing.lg,
    borderWidth: 1,
    borderColor: THEME.colors.border,
    marginBottom: THEME.spacing.md,
    ...THEME.shadow.sm,
  },
  sectionHeading: {
    fontSize: THEME.typography.sizes.xs,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.textMuted,
    textTransform: 'uppercase',
    letterSpacing: 0.5,
    marginBottom: THEME.spacing.md,
  },
  infoRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: THEME.spacing.sm,
    borderBottomWidth: 1,
    borderBottomColor: THEME.colors.surfaceVariant,
  },
  infoLabel: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textSecondary,
  },
  infoVal: {
    fontSize: THEME.typography.sizes.xs,
    fontWeight: THEME.typography.weights.semibold,
    color: THEME.colors.text,
  },
  switchRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  switchTitle: {
    fontSize: THEME.typography.sizes.sm,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.text,
  },
  switchSubtitle: {
    fontSize: 11,
    color: THEME.colors.textSecondary,
    marginTop: 2,
    lineHeight: 16,
  },
  serverRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  serverLabel: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textSecondary,
  },
  serverValue: {
    fontSize: THEME.typography.sizes.xs,
    fontWeight: THEME.typography.weights.semibold,
    color: THEME.colors.primary,
    marginTop: 2,
  },
  logoutBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: THEME.colors.surface,
    borderWidth: 1,
    borderColor: THEME.colors.error + '44',
    paddingVertical: THEME.spacing.md,
    borderRadius: THEME.borderRadius.md,
    gap: 8,
    marginTop: THEME.spacing.sm,
  },
  logoutBtnText: {
    fontSize: THEME.typography.sizes.sm,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.error,
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
  },
  modalSub: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textSecondary,
    marginTop: 4,
    marginBottom: THEME.spacing.md,
    lineHeight: 16,
  },
  modalInput: {
    backgroundColor: THEME.colors.surfaceVariant,
    borderRadius: THEME.borderRadius.md,
    borderWidth: 1,
    borderColor: THEME.colors.border,
    padding: THEME.spacing.md,
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.text,
    marginBottom: THEME.spacing.md,
  },
  statusBox: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    padding: THEME.spacing.sm,
    borderRadius: THEME.borderRadius.sm,
    marginBottom: THEME.spacing.md,
  },
  statusBoxSuccess: {
    backgroundColor: THEME.colors.primaryMuted,
  },
  statusBoxError: {
    backgroundColor: THEME.colors.errorMuted,
  },
  statusBoxText: {
    fontSize: 11,
    flex: 1,
  },
  textSuccess: {
    color: THEME.colors.success,
  },
  textError: {
    color: THEME.colors.error,
  },
  modalButtons: {
    flexDirection: 'row',
    justifyContent: 'flex-end',
    gap: THEME.spacing.sm,
  },
  modalCloseBtn: {
    paddingHorizontal: THEME.spacing.md,
    paddingVertical: THEME.spacing.sm,
    borderRadius: THEME.borderRadius.sm,
  },
  modalCloseText: {
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
