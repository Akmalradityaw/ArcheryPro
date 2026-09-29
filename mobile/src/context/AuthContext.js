import React, { createContext, useContext, useState, useEffect } from 'react';
import { ApiService } from '../services/api';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [token, setToken] = useState(null);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    bootstrapAuth();
  }, []);

  const bootstrapAuth = async () => {
    try {
      const stored = await ApiService.getStoredAuth();
      if (stored.token && stored.user) {
        setToken(stored.token);
        setUser(stored.user);

        // Verifikasi token masih valid di backend secara asynchronous
        try {
          const res = await ApiService.get('/auth/me');
          if (res?.data) {
            setUser(res.data);
            await ApiService.saveAuth(stored.token, res.data);
          }
        } catch (e) {
          if (e.status === 401) {
            // Token expired atau dicabut
            await ApiService.clearAuth();
            setToken(null);
            setUser(null);
          }
        }
      }
    } catch (e) {
      console.warn('Gagal memuat sesi auth lokal', e);
    } finally {
      setIsLoading(false);
    }
  };

  const login = async (username, password) => {
    const res = await ApiService.post('/auth/login', { username, password });
    if (res?.data?.token && res?.data?.user) {
      const authToken = res.data.token;
      const authUser = res.data.user;

      await ApiService.saveAuth(authToken, authUser);
      setToken(authToken);
      setUser(authUser);
      return authUser;
    }
    throw new Error('Format respon login tidak valid.');
  };

  const logout = async () => {
    try {
      await ApiService.post('/auth/logout').catch(() => {});
    } finally {
      await ApiService.clearAuth();
      setToken(null);
      setUser(null);
    }
  };

  const refreshProfile = async () => {
    try {
      const res = await ApiService.get('/auth/me');
      if (res?.data) {
        setUser(res.data);
        if (token) {
          await ApiService.saveAuth(token, res.data);
        }
      }
    } catch (e) {
      console.warn('Gagal memperbarui profil auth', e);
    }
  };

  return (
    <AuthContext.Provider
      value={{
        user,
        token,
        role: user?.role || null,
        atlet: user?.atlet || null,
        isAuthenticated: !!token && !!user,
        isLoading,
        login,
        logout,
        refreshProfile,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth harus digunakan di dalam AuthProvider');
  }
  return context;
}
