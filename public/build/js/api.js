/**
 * API 请求封装 - Admin & Unit 共用
 * 401 自动跳转登录
 */
(function() {
  const API_BASE = window.APP_BASE || '/admin';
  const TOKEN_KEY = window.APP_TOKEN_KEY || 'token';
  const USER_KEY = (API_BASE === '/unit' ? 'unit_user' : 'admin_user');

  function apiBase() {
    return API_BASE + (window.APP_API_SUFFIX !== undefined ? (window.APP_API_SUFFIX || '') : '/api');
  }

  async function api(url, opts = {}) {
    const token = localStorage.getItem(TOKEN_KEY);
    const path = (url.startsWith('/') ? url : '/' + url);
    const res = await fetch(apiBase() + path, {
      ...opts,
      headers: {
        'Content-Type': 'application/json',
        ...(token ? { 'Authorization': 'Bearer ' + token } : {}),
        ...opts.headers,
      },
    });
    const data = await res.json().catch(() => ({}));
    if (res.status === 401 || data.code === 401) {
      localStorage.removeItem(TOKEN_KEY);
      localStorage.removeItem(USER_KEY);
      location.href = API_BASE + '/login';
      return;
    }
    if (data.code !== 0 && data.code !== undefined) {
      throw new Error(data.message || '请求失败');
    }
    return data;
  }

  async function apiFormData(url, formData) {
    const token = localStorage.getItem(TOKEN_KEY);
    const path = (url.startsWith('/') ? url : '/' + url);
    const res = await fetch(apiBase() + path, {
      method: 'POST',
      headers: token ? { 'Authorization': 'Bearer ' + token } : {},
      body: formData,
    });
    const data = await res.json().catch(() => ({}));
    if (res.status === 401 || data.code === 401) {
      localStorage.removeItem(TOKEN_KEY);
      localStorage.removeItem(USER_KEY);
      location.href = API_BASE + '/login';
      return;
    }
    if (data.code !== 0 && data.code !== undefined) throw new Error(data.message || '上传失败');
    return data;
  }

  /**
   * 将 ISO 时间格式转为本地可读格式，如 2026-01-31 14:20:16
   */
  function formatDateTime(str) {
    if (!str) return '-';
    try {
      const d = new Date(str);
      if (isNaN(d.getTime())) return str;
      return d.getFullYear() + '-' +
        String(d.getMonth() + 1).padStart(2, '0') + '-' +
        String(d.getDate()).padStart(2, '0') + ' ' +
        String(d.getHours()).padStart(2, '0') + ':' +
        String(d.getMinutes()).padStart(2, '0') + ':' +
        String(d.getSeconds()).padStart(2, '0');
    } catch (e) {
      return str;
    }
  }

  window.api = api;
  window.apiFormData = apiFormData;
  window.formatDateTime = formatDateTime;
})();
