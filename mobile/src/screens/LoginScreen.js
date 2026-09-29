import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  ActivityIndicator,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
  Alert,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../context/AuthContext';
import { ApiService } from '../services/api';
import { THEME } from '../constants/theme';

export function LoginScreen() {
  const { login } = useAuth();
  const [username, setUsername] = useState('pelatih1');
  const [password, setPassword] = useState('password123');
  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);
  const [showServerConfig, setShowServerConfig] = useState(false);
  const [serverUrl, setServerUrl] = useState('');

  useEffect(() => {
    ApiService.getBaseUrl().then(url => setServerUrl(url));
  }, []);

  const handleLogin = async () => {
    if (!username.trim() || !password.trim()) {
      Alert.alert('Perhatian', 'Harap masukkan username dan password.');
      return;
    }

    try {
      setLoading(true);
      await login(username.trim(), password);
    } catch (err) {
      Alert.alert('Login Gagal', err.message || 'Kredensial tidak valid atau server tidak merespon.');
    } finally {
      setLoading(false);
    }
  };

  const handleSaveServerUrl = async () => {
    try {
      const updated = await ApiService.setBaseUrl(serverUrl);
      setServerUrl(updated);
      setShowServerConfig(false);
      Alert.alert('Sukses', `Alamat server diperbarui ke:\n${updated}`);
    } catch {
      Alert.alert('Error', 'Gagal menyimpan URL server.');
    }
  };

  const quickFill = (user, pass) => {
    setUsername(user);
    setPassword(pass);
  };

  return (
    <KeyboardAvoidingView
      style={styles.container}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
    >
      <ScrollView contentContainerStyle={styles.scrollContent} keyboardShouldPersistTaps="handled">
        {/* Logo & Brand Header */}
        <View style={styles.brandContainer}>
          <View style={styles.logoCircle}>
            <Ionicons name="disc" size={38} color={THEME.colors.primary} />
          </View>
          <Text style={styles.brandTitle}>ArcheryPro Mobile</Text>
          <Text style={styles.brandSubtitle}>Pencatatan Skor & Monitoring Latihan Mingguan v1.0.0</Text>
        </View>

        {/* Card Form */}
        <View style={styles.card}>
          <Text style={styles.formTitle}>Masuk ke Akun</Text>
          <Text style={styles.formSubtitle}>Pilih peran atau masukkan username & password</Text>

          {/* Quick Account Selector Chips */}
          <Text style={styles.quickLabel}>Pilih Akun Demo Standar:</Text>
          <View style={styles.quickRow}>
            <TouchableOpacity
              style={[styles.quickChip, username === 'pelatih1' && styles.quickChipActive]}
              onPress={() => quickFill('pelatih1', 'password123')}
            >
              <Text style={[styles.quickChipText, username === 'pelatih1' && styles.quickChipTextActive]}>
                Pelatih (Bambang)
              </Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[styles.quickChip, username === 'rizky1' && styles.quickChipActive]}
              onPress={() => quickFill('rizky1', 'password123')}
            >
              <Text style={[styles.quickChipText, username === 'rizky1' && styles.quickChipTextActive]}>
                Atlet (Rizky)
              </Text>
            </TouchableOpacity>
          </View>

          <View style={[styles.quickRow, { marginBottom: THEME.spacing.md }]}>
            <TouchableOpacity
              style={[styles.quickChip, username === 'scoring1' && styles.quickChipActive]}
              onPress={() => quickFill('scoring1', 'password123')}
            >
              <Text style={[styles.quickChipText, username === 'scoring1' && styles.quickChipTextActive]}>
                Petugas Scoring
              </Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[styles.quickChip, username === 'dewi2' && styles.quickChipActive]}
              onPress={() => quickFill('dewi2', 'password123')}
            >
              <Text style={[styles.quickChipText, username === 'dewi2' && styles.quickChipTextActive]}>
                Atlet (Dewi)
              </Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[styles.quickChip, username === 'admin' && styles.quickChipActive]}
              onPress={() => quickFill('admin', 'admin123')}
            >
              <Text style={[styles.quickChipText, username === 'admin' && styles.quickChipTextActive]}>
                Admin
              </Text>
            </TouchableOpacity>
          </View>

          {/* Input Username */}
          <View style={styles.inputGroup}>
            <Text style={styles.label}>Username</Text>
            <View style={styles.inputWrapper}>
              <Ionicons name="person-outline" size={18} color={THEME.colors.textSecondary} style={styles.inputIcon} />
              <TextInput
                style={styles.input}
                value={username}
                onChangeText={setUsername}
                placeholder="Masukkan username"
                placeholderTextColor={THEME.colors.textMuted}
                autoCapitalize="none"
                autoCorrect={false}
              />
            </View>
          </View>

          {/* Input Password */}
          <View style={styles.inputGroup}>
            <Text style={styles.label}>Password</Text>
            <View style={styles.inputWrapper}>
              <Ionicons name="lock-closed-outline" size={18} color={THEME.colors.textSecondary} style={styles.inputIcon} />
              <TextInput
                style={styles.input}
                value={password}
                onChangeText={setPassword}
                placeholder="Masukkan password"
                placeholderTextColor={THEME.colors.textMuted}
                secureTextEntry={!showPassword}
                autoCapitalize="none"
              />
              <TouchableOpacity onPress={() => setShowPassword(!showPassword)} style={styles.togglePassword}>
                <Ionicons
                  name={showPassword ? 'eye-off-outline' : 'eye-outline'}
                  size={18}
                  color={THEME.colors.textSecondary}
                />
              </TouchableOpacity>
            </View>
          </View>

          {/* Tombol Login */}
          <TouchableOpacity
            style={[styles.loginButton, loading && styles.loginButtonDisabled]}
            onPress={handleLogin}
            disabled={loading}
            activeOpacity={0.8}
          >
            {loading ? (
              <ActivityIndicator color="#FFFFFF" size="small" />
            ) : (
              <>
                <Text style={styles.loginButtonText}>Masuk Sekarang</Text>
                <Ionicons name="arrow-forward" size={18} color="#FFFFFF" style={{ marginLeft: 6 }} />
              </>
            )}
          </TouchableOpacity>

          {/* Toggle Konfigurasi Server */}
          <TouchableOpacity
            onPress={() => setShowServerConfig(!showServerConfig)}
            style={styles.serverToggle}
          >
            <Ionicons name="server-outline" size={14} color={THEME.colors.primary} />
            <Text style={styles.serverToggleText}>
              {showServerConfig ? 'Tutup Pengaturan Server' : `Server: ${serverUrl || 'Memuat...'}`}
            </Text>
          </TouchableOpacity>

          {showServerConfig && (
            <View style={styles.serverConfigBox}>
              <Text style={styles.serverConfigLabel}>Alamat API Backend:</Text>
              <TextInput
                style={styles.serverConfigInput}
                value={serverUrl}
                onChangeText={setServerUrl}
                autoCapitalize="none"
                placeholder="http://192.168.1.4:8000/api/v1"
                placeholderTextColor={THEME.colors.textMuted}
              />
              <TouchableOpacity style={styles.serverSaveButton} onPress={handleSaveServerUrl}>
                <Text style={styles.serverSaveButtonText}>Simpan Alamat Server</Text>
              </TouchableOpacity>
              <Text style={styles.serverHint}>
                *HP Fisik via WiFi: pastikan menggunakan IP WiFi PC (contoh: http://192.168.1.4:8000/api/v1).
              </Text>
            </View>
          )}
        </View>

        <Text style={styles.footerNote}>ArcheryPro Mobile • Weekly Practice REST API</Text>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: THEME.colors.background,
  },
  scrollContent: {
    flexGrow: 1,
    justifyContent: 'center',
    padding: THEME.spacing.xl,
  },
  brandContainer: {
    alignItems: 'center',
    marginBottom: THEME.spacing.xl,
  },
  logoCircle: {
    width: 72,
    height: 72,
    borderRadius: THEME.borderRadius.full,
    backgroundColor: THEME.colors.primaryMuted,
    borderWidth: 2,
    borderColor: THEME.colors.primary + '33',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: THEME.spacing.md,
    ...THEME.shadow.sm,
  },
  brandTitle: {
    fontSize: THEME.typography.sizes.xxl,
    fontWeight: THEME.typography.weights.black,
    color: THEME.colors.text,
    letterSpacing: -0.5,
  },
  brandSubtitle: {
    fontSize: THEME.typography.sizes.sm,
    color: THEME.colors.textSecondary,
    marginTop: 4,
    textAlign: 'center',
  },
  card: {
    backgroundColor: THEME.colors.surface,
    borderRadius: THEME.borderRadius.xl,
    padding: THEME.spacing.xl,
    borderWidth: 1,
    borderColor: THEME.colors.border,
    ...THEME.shadow.md,
  },
  formTitle: {
    fontSize: THEME.typography.sizes.lg,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.text,
  },
  formSubtitle: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.textSecondary,
    marginTop: 2,
    marginBottom: THEME.spacing.md,
  },
  quickLabel: {
    fontSize: 10,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.textMuted,
    textTransform: 'uppercase',
    marginBottom: 6,
  },
  quickRow: {
    flexDirection: 'row',
    gap: 6,
    marginBottom: THEME.spacing.md,
  },
  quickChip: {
    flex: 1,
    paddingVertical: 6,
    borderRadius: THEME.borderRadius.sm,
    backgroundColor: THEME.colors.surfaceVariant,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: THEME.colors.border,
  },
  quickChipActive: {
    backgroundColor: THEME.colors.primaryMuted,
    borderColor: THEME.colors.primary,
  },
  quickChipText: {
    fontSize: 11,
    fontWeight: THEME.typography.weights.semibold,
    color: THEME.colors.textSecondary,
  },
  quickChipTextActive: {
    color: THEME.colors.primary,
    fontWeight: THEME.typography.weights.bold,
  },
  inputGroup: {
    marginBottom: THEME.spacing.md,
  },
  label: {
    fontSize: THEME.typography.sizes.xs,
    fontWeight: THEME.typography.weights.semibold,
    color: THEME.colors.text,
    marginBottom: 6,
  },
  inputWrapper: {
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: THEME.colors.border,
    borderRadius: THEME.borderRadius.md,
    backgroundColor: THEME.colors.surface,
    paddingHorizontal: THEME.spacing.md,
    height: 46,
  },
  inputIcon: {
    marginRight: THEME.spacing.sm,
  },
  input: {
    flex: 1,
    fontSize: THEME.typography.sizes.sm,
    color: THEME.colors.text,
  },
  togglePassword: {
    padding: THEME.spacing.xs,
  },
  loginButton: {
    backgroundColor: THEME.colors.primary,
    borderRadius: THEME.borderRadius.md,
    height: 48,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: THEME.spacing.md,
    ...THEME.shadow.sm,
  },
  loginButtonDisabled: {
    opacity: 0.7,
  },
  loginButtonText: {
    color: '#FFFFFF',
    fontSize: THEME.typography.sizes.md,
    fontWeight: THEME.typography.weights.bold,
  },
  serverToggle: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: THEME.spacing.lg,
    gap: 6,
  },
  serverToggleText: {
    fontSize: THEME.typography.sizes.xs,
    color: THEME.colors.primary,
    fontWeight: THEME.typography.weights.semibold,
  },
  serverConfigBox: {
    marginTop: THEME.spacing.md,
    padding: THEME.spacing.md,
    backgroundColor: THEME.colors.surfaceVariant,
    borderRadius: THEME.borderRadius.md,
    borderWidth: 1,
    borderColor: THEME.colors.border,
  },
  serverConfigLabel: {
    fontSize: 11,
    fontWeight: THEME.typography.weights.semibold,
    color: THEME.colors.textSecondary,
    marginBottom: 4,
  },
  serverConfigInput: {
    backgroundColor: '#FFFFFF',
    borderWidth: 1,
    borderColor: THEME.colors.border,
    borderRadius: THEME.borderRadius.sm,
    paddingHorizontal: THEME.spacing.sm,
    height: 38,
    fontSize: 12,
    color: THEME.colors.text,
  },
  serverSaveButton: {
    backgroundColor: THEME.colors.primary,
    borderRadius: THEME.borderRadius.sm,
    paddingVertical: 6,
    alignItems: 'center',
    marginTop: 8,
  },
  serverSaveButtonText: {
    color: '#FFFFFF',
    fontSize: 11,
    fontWeight: THEME.typography.weights.semibold,
  },
  serverHint: {
    fontSize: 10,
    color: THEME.colors.textMuted,
    marginTop: 6,
    fontStyle: 'italic',
  },
  footerNote: {
    textAlign: 'center',
    fontSize: 11,
    color: THEME.colors.textMuted,
    marginTop: THEME.spacing.xl,
  },
});
