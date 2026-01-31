/**
 * 应用状态 - Alpine.js 共用
 */
function appState() {
  const API_BASE = window.APP_BASE || '/admin';
  const TOKEN_KEY = window.APP_TOKEN_KEY || 'token';
  const USER_KEY = (API_BASE === '/unit' ? 'unit_user' : 'admin_user');
  return {
    user: JSON.parse(localStorage.getItem(USER_KEY) || '{}'),
    token: localStorage.getItem(TOKEN_KEY) || '',
    async logout() {
      localStorage.removeItem(TOKEN_KEY);
      localStorage.removeItem(USER_KEY);
      location.href = API_BASE + '/login';
    }
  };
}
