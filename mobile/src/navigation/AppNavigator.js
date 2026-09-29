import React, { useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, SafeAreaView, Platform } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { THEME } from '../constants/theme';
import { useAuth } from '../context/AuthContext';
import { Header } from '../components/Header';

// Screens
import { DashboardScreen } from '../screens/DashboardScreen';
import { SesiLatihanScreen } from '../screens/SesiLatihanScreen';
import { InputSkorScreen } from '../screens/InputSkorScreen';
import { PerformaScreen } from '../screens/PerformaScreen';
import { ProfilScreen } from '../screens/ProfilScreen';

export function AppNavigator() {
  const { role, user } = useAuth();
  const [currentScreen, setCurrentScreen] = useState('Dashboard');
  const [routeParams, setRouteParams] = useState({});

  // Navigation Controller mock
  const navigation = {
    navigate: (screenName, params = {}) => {
      setRouteParams(params);
      setCurrentScreen(screenName);
    },
    goBack: () => {
      setCurrentScreen('Dashboard');
      setRouteParams({});
    },
  };

  // Tab definitions based on user role
  const isCoach = role === 'pelatih' || role === 'admin';

  const tabs = isCoach
    ? [
        { name: 'Dashboard', label: 'Beranda', icon: 'home-outline', iconActive: 'home' },
        { name: 'SesiLatihan', label: 'Jadwal', icon: 'calendar-outline', iconActive: 'calendar' },
        { name: 'InputSkor', label: 'Input Skor', icon: 'create-outline', iconActive: 'create' },
        { name: 'Performa', label: 'Performa', icon: 'bar-chart-outline', iconActive: 'bar-chart' },
        { name: 'Profil', label: 'Profil', icon: 'person-outline', iconActive: 'person' },
      ]
    : [
        { name: 'Dashboard', label: 'Beranda', icon: 'home-outline', iconActive: 'home' },
        { name: 'SesiLatihan', label: 'Jadwal', icon: 'calendar-outline', iconActive: 'calendar' },
        { name: 'Performa', label: 'Performa', icon: 'bar-chart-outline', iconActive: 'bar-chart' },
        { name: 'Profil', label: 'Profil', icon: 'person-outline', iconActive: 'person' },
      ];

  const getHeaderProps = () => {
    switch (currentScreen) {
      case 'Dashboard':
        return {
          title: 'ArcheryPro',
          subtitle: 'Klub Panahan Profesional',
          badgeText: isCoach ? 'Pelatih' : 'Atlet',
          onBack: null,
        };
      case 'SesiLatihan':
        return {
          title: 'Jadwal Latihan',
          subtitle: 'Sesi latihan mingguan & hasil skor',
          onBack: () => navigation.navigate('Dashboard'),
        };
      case 'InputSkor':
        return {
          title: 'Input Skor Latihan',
          subtitle: 'Pencatatan tembakan panah mingguan',
          onBack: () => navigation.navigate('Dashboard'),
        };
      case 'Performa':
        return {
          title: isCoach ? 'Analisis Performa Atlet' : 'Performa & Riwayat',
          subtitle: isCoach ? 'Evaluasi teknis dan konsistensi tembakan' : 'Statistik dan lencana latihan',
          onBack: () => navigation.navigate('Dashboard'),
        };
      case 'Profil':
        return {
          title: 'Profil Pengguna',
          subtitle: user?.nama_lengkap || user?.username,
          onBack: () => navigation.navigate('Dashboard'),
        };
      default:
        return { title: 'ArcheryPro', subtitle: '' };
    }
  };

  const renderActiveScreen = () => {
    const route = { params: routeParams };
    switch (currentScreen) {
      case 'Dashboard':
        return <DashboardScreen navigation={navigation} route={route} />;
      case 'SesiLatihan':
        return <SesiLatihanScreen navigation={navigation} route={route} />;
      case 'InputSkor':
        return <InputSkorScreen navigation={navigation} route={route} />;
      case 'Performa':
        return <PerformaScreen navigation={navigation} route={route} />;
      case 'Profil':
        return <ProfilScreen navigation={navigation} route={route} />;
      default:
        return <DashboardScreen navigation={navigation} route={route} />;
    }
  };

  const headerProps = getHeaderProps();

  return (
    <View style={styles.container}>
      {/* Top Header */}
      <Header
        title={headerProps.title}
        subtitle={headerProps.subtitle}
        onBack={headerProps.onBack}
        badgeText={headerProps.badgeText}
      />

      {/* Main Screen Body */}
      <View style={styles.body}>{renderActiveScreen()}</View>

      {/* Bottom Tab Bar */}
      <SafeAreaView style={styles.tabBarSafeArea}>
        <View style={styles.tabBar}>
          {tabs.map((tab) => {
            const isActive = currentScreen === tab.name;
            return (
              <TouchableOpacity
                key={tab.name}
                style={styles.tabItem}
                onPress={() => navigation.navigate(tab.name)}
                activeOpacity={0.7}
              >
                <View style={[styles.tabIconWrap, isActive && styles.tabIconWrapActive]}>
                  <Ionicons
                    name={isActive ? tab.iconActive : tab.icon}
                    size={20}
                    color={isActive ? THEME.colors.primary : THEME.colors.textMuted}
                  />
                </View>
                <Text style={[styles.tabLabel, isActive && styles.tabLabelActive]}>
                  {tab.label}
                </Text>
                {isActive && <View style={styles.activeDot} />}
              </TouchableOpacity>
            );
          })}
        </View>
      </SafeAreaView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: THEME.colors.background,
  },
  body: {
    flex: 1,
  },
  tabBarSafeArea: {
    backgroundColor: THEME.colors.surface,
    borderTopWidth: 1,
    borderTopColor: THEME.colors.border,
  },
  tabBar: {
    height: 60,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-around',
    backgroundColor: THEME.colors.surface,
    paddingHorizontal: THEME.spacing.sm,
  },
  tabItem: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 4,
  },
  tabIconWrap: {
    width: 32,
    height: 32,
    borderRadius: THEME.borderRadius.sm,
    alignItems: 'center',
    justifyContent: 'center',
  },
  tabIconWrapActive: {
    backgroundColor: THEME.colors.primaryMuted,
  },
  tabLabel: {
    fontSize: 10,
    fontWeight: THEME.typography.weights.medium,
    color: THEME.colors.textMuted,
    marginTop: 2,
  },
  tabLabelActive: {
    color: THEME.colors.primary,
    fontWeight: THEME.typography.weights.bold,
  },
  activeDot: {
    width: 4,
    height: 4,
    borderRadius: 2,
    backgroundColor: THEME.colors.primary,
    marginTop: 2,
  },
});
