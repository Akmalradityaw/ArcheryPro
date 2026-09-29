import React from 'react';
import { StatusBar } from 'expo-status-bar';
import { StyleSheet, View, Text, ActivityIndicator } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { AuthProvider, useAuth } from './src/context/AuthContext';
import { LoginScreen } from './src/screens/LoginScreen';
import { AppNavigator } from './src/navigation/AppNavigator';
import { THEME } from './src/constants/theme';

function RootApp() {
  const { isAuthenticated, isLoading } = useAuth();

  if (isLoading) {
    return (
      <View style={styles.splashContainer}>
        <View style={styles.splashIconCircle}>
          <Ionicons name="disc-outline" size={48} color={THEME.colors.surface} />
        </View>
        <Text style={styles.splashTitle}>ArcheryPro</Text>
        <Text style={styles.splashSubtitle}>Sistem Panahan Digital</Text>
        <ActivityIndicator size="small" color={THEME.colors.primary} style={{ marginTop: 24 }} />
      </View>
    );
  }

  return (
    <View style={styles.appContainer}>
      <StatusBar style="dark" />
      {isAuthenticated ? <AppNavigator /> : <LoginScreen />}
    </View>
  );
}

export default function App() {
  return (
    <AuthProvider>
      <RootApp />
    </AuthProvider>
  );
}

const styles = StyleSheet.create({
  appContainer: {
    flex: 1,
    backgroundColor: THEME.colors.background,
  },
  splashContainer: {
    flex: 1,
    backgroundColor: THEME.colors.surface,
    alignItems: 'center',
    justifyContent: 'center',
    padding: THEME.spacing.xl,
  },
  splashIconCircle: {
    width: 80,
    height: 80,
    borderRadius: THEME.borderRadius.full,
    backgroundColor: THEME.colors.primary,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: THEME.spacing.md,
    ...THEME.shadow.md,
  },
  splashTitle: {
    fontSize: THEME.typography.sizes.xxl,
    fontWeight: THEME.typography.weights.bold,
    color: THEME.colors.text,
  },
  splashSubtitle: {
    fontSize: THEME.typography.sizes.sm,
    color: THEME.colors.textSecondary,
    marginTop: 4,
  },
});
