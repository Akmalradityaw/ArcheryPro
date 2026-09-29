import AsyncStorage from '@react-native-async-storage/async-storage';
import { Platform, NativeModules } from 'react-native';

const STORAGE_KEYS = {
  TOKEN: '@archerypro_auth_token',
  USER: '@archerypro_auth_user',
  BASE_URL: '@archerypro_base_url',
  OFFLINE_DRAFT: '@archerypro_score_draft',
};

// Deteksi otomatis IP host dev machine jika dijalankan melalui Expo Go
const getDetectedHost = () => {
  try {
    const scriptURL = NativeModules.SourceCode?.scriptURL;
    if (scriptURL) {
      const match = scriptURL.match(/https?:\/\/([^:/]+)/);
      if (match && match[1] && match[1] !== 'localhost' && match[1] !== '127.0.0.1') {
        return match[1];
      }
    }
  } catch {}
  return null;
};

const detectedHost = getDetectedHost();

// Default URL fallback sesuai platform & environment:
// - Jika Expo mendeteksi IP PC (misal 192.168.1.4), gunakan IP tersebut
// - Default Android fallback ke IP WiFi PC 192.168.1.4 (bisa diakses HP fisik & emulator)
// - Default Web / Simulator fallback ke localhost
const DEFAULT_BASE_URL = detectedHost
  ? `http://${detectedHost}:8000/api/v1`
  : Platform.select({
      android: 'http://192.168.1.4:8000/api/v1',
      default: 'http://localhost:8000/api/v1',
    });

let cachedBaseUrl = null;

export const ApiService = {
  /**
   * Dapatkan URL backend aktif
   */
  async getBaseUrl() {
    if (cachedBaseUrl) return cachedBaseUrl;
    try {
      const saved = await AsyncStorage.getItem(STORAGE_KEYS.BASE_URL);
      cachedBaseUrl = saved || DEFAULT_BASE_URL;
      return cachedBaseUrl;
    } catch {
      return DEFAULT_BASE_URL;
    }
  },

  /**
   * Ubah URL backend (berguna untuk testing di perangkat HP fisik via IP WiFi)
   */
  async setBaseUrl(newUrl) {
    let clean = (newUrl || '').trim();
    if (!clean) clean = DEFAULT_BASE_URL;
    if (!clean.endsWith('/api/v1')) {
      clean = clean.replace(/\/+$/, '') + '/api/v1';
    }
    await AsyncStorage.setItem(STORAGE_KEYS.BASE_URL, clean);
    cachedBaseUrl = clean;
    return clean;
  },

  // Alias untuk kemudahan pemanggilan
  async setUrl(newUrl) {
    return this.setBaseUrl(newUrl);
  },

  /**
   * Request helper internal
   */
  async request(endpoint, options = {}) {
    const baseUrl = await this.getBaseUrl();
    const token = await AsyncStorage.getItem(STORAGE_KEYS.TOKEN);

    const headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      ...(token ? { 'Authorization': `Bearer ${token}` } : {}),
      ...(options.headers || {}),
    };

    const cleanEndpoint = endpoint.startsWith('/') ? endpoint : `/${endpoint}`;
    const url = `${baseUrl}${cleanEndpoint}`;

    let timeoutId = null;
    try {
      const controller = new AbortController();
      timeoutId = setTimeout(() => {
        try { controller.abort(); } catch {}
      }, 15000); // 15 detik timeout

      const res = await fetch(url, {
        ...options,
        headers,
        signal: controller.signal,
      });

      if (timeoutId) clearTimeout(timeoutId);

      const json = await res.json().catch(() => null);

      if (!res.ok) {
        const errorMsg = json?.message || (json?.errors ? Object.values(json.errors).flat().join(', ') : `HTTP Error ${res.status}`);
        const error = new Error(errorMsg);
        error.status = res.status;
        error.data = json;
        throw error;
      }

      return json;
    } catch (err) {
      if (timeoutId) clearTimeout(timeoutId);

      const msg = (err?.message || '').toLowerCase();
      const isNetworkOrCancel =
        err?.name === 'AbortError' ||
        msg.includes('canceled') ||
        msg.includes('cancelled') ||
        msg.includes('network request failed') ||
        msg.includes('failed to fetch');

      if (isNetworkOrCancel) {
        throw new Error(
          `Gagal terhubung ke server REST API (${baseUrl}).\n\nPastikan 'php artisan serve' sedang berjalan di PC dan perangkat HP terhubung ke jaringan yang sama.`
        );
      }
      throw err;
    }
  },

  // HTTP Methods
  async get(endpoint, params = null) {
    let query = '';
    if (params) {
      const filtered = Object.entries(params)
        .filter(([_, v]) => v !== null && v !== undefined && v !== '')
        .map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`)
        .join('&');
      if (filtered) query = `?${filtered}`;
    }
    return this.request(`${endpoint}${query}`, { method: 'GET' });
  },

  async post(endpoint, data = {}) {
    return this.request(endpoint, {
      method: 'POST',
      body: JSON.stringify(data),
    });
  },

  async put(endpoint, data = {}) {
    return this.request(endpoint, {
      method: 'PUT',
      body: JSON.stringify(data),
    });
  },

  async delete(endpoint) {
    return this.request(endpoint, { method: 'DELETE' });
  },

  // Auth Storage Helpers
  async saveAuth(token, user) {
    await AsyncStorage.multiSet([
      [STORAGE_KEYS.TOKEN, token],
      [STORAGE_KEYS.USER, JSON.stringify(user)],
    ]);
  },

  async clearAuth() {
    await AsyncStorage.multiRemove([STORAGE_KEYS.TOKEN, STORAGE_KEYS.USER]);
  },

  async getStoredAuth() {
    const [[, token], [, userStr]] = await AsyncStorage.multiGet([
      STORAGE_KEYS.TOKEN,
      STORAGE_KEYS.USER,
    ]);
    return {
      token,
      user: userStr ? JSON.parse(userStr) : null,
    };
  },

  // Offline Draft Helpers
  async saveOfflineDraft(draftData) {
    await AsyncStorage.setItem(
      STORAGE_KEYS.OFFLINE_DRAFT,
      JSON.stringify({
        ...draftData,
        savedAt: new Date().toISOString(),
      })
    );
  },

  async getOfflineDraft() {
    const raw = await AsyncStorage.getItem(STORAGE_KEYS.OFFLINE_DRAFT);
    return raw ? JSON.parse(raw) : null;
  },

  async clearOfflineDraft() {
    await AsyncStorage.removeItem(STORAGE_KEYS.OFFLINE_DRAFT);
  },
};
