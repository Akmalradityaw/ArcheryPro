export const THEME = {
  colors: {
    primary: '#2E7D32',       // Solid Green Forest
    primaryDark: '#1B5E20',   // Deep Green
    primaryLight: '#4CAF50',  // Vivid Green
    primaryMuted: '#E8F5E9',  // Soft Green background
    
    background: '#F8FAFC',
    surface: '#FFFFFF',
    surfaceVariant: '#F1F5F9',
    
    text: '#0F172A',
    textSecondary: '#475569',
    textMuted: '#94A3B8',
    
    border: '#E2E8F0',
    borderFocus: '#2E7D32',
    
    success: '#16A34A',
    warning: '#D97706',
    warningMuted: '#FEF3C7',
    error: '#DC2626',
    errorMuted: '#FEE2E2',
    info: '#2563EB',
    infoMuted: '#DBEAFE',

    badgeGold: '#F59E0B',
    badgeSilver: '#94A3B8',
    badgeBronze: '#D97706',
  },
  typography: {
    fontFamily: 'System',
    sizes: {
      xs: 11,
      sm: 13,
      md: 15,
      lg: 18,
      xl: 22,
      xxl: 26,
    },
    weights: {
      regular: '400',
      medium: '500',
      semibold: '600',
      bold: '700',
      black: '800',
    }
  },
  spacing: {
    xs: 4,
    sm: 8,
    md: 12,
    lg: 16,
    xl: 20,
    xxl: 24,
    card: 16,
  },
  borderRadius: {
    sm: 8,
    md: 12,
    lg: 16,
    xl: 20,
    full: 9999,
  },
  shadow: {
    sm: {
      shadowColor: '#0F172A',
      shadowOffset: { width: 0, height: 1 },
      shadowOpacity: 0.05,
      shadowRadius: 2,
      elevation: 1,
    },
    md: {
      shadowColor: '#0F172A',
      shadowOffset: { width: 0, height: 2 },
      shadowOpacity: 0.08,
      shadowRadius: 4,
      elevation: 2,
    },
    lg: {
      shadowColor: '#0F172A',
      shadowOffset: { width: 0, height: 4 },
      shadowOpacity: 0.12,
      shadowRadius: 8,
      elevation: 4,
    }
  }
};
