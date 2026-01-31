/**
 * 共享前端组件 - Admin & Unit 共用
 * 风格: 深色简洁，emerald 主色
 */

const API_BASE = window.APP_BASE || '/admin';
const API_SUFFIX = window.APP_API_SUFFIX || '/api'; // admin/unit 的 API 子路径
const TOKEN_KEY = window.APP_TOKEN_KEY || 'token'; // 管理端 admin_token / 单位端 unit_token，与登录端一致

// ==================== 状态 ====================
function appState() {
  return {
    user: JSON.parse(localStorage.getItem('user') || '{}'),
    token: localStorage.getItem(TOKEN_KEY) || '',
    async logout() {
      localStorage.removeItem(TOKEN_KEY);
      localStorage.removeItem('user');
      location.href = API_BASE + '/login';
    }
  };
}

// ==================== 请求封装 ====================
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
  if (data.code !== 0 && data.code !== undefined) throw new Error(data.message || '上传失败');
  return data;
}

// ==================== 统一弹窗 ====================
window.Modal = {
  open(options = {}) {
    const { title = '', body = '', confirmText = '确定', onConfirm } = options;
    const isDark = document.documentElement.classList.contains('dark');
    let root = document.getElementById('modal-root');
    if (!root) {
      root = document.createElement('div');
      root.id = 'modal-root';
      document.body.appendChild(root);
    }
    root.innerHTML = `
      <div id="modal-instance" class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-sm" style="background: ${isDark ? 'rgba(0,0,0,0.7)' : 'rgba(0,0,0,0.5)'}; animation: fadeIn 0.2s ease-out;">
        <div class="relative w-full max-w-lg rounded-2xl backdrop-blur-xl border shadow-2xl ${isDark ? 'bg-slate-900/95 border-slate-700/50' : 'bg-white/95 border-slate-200'}" style="animation: scaleIn 0.2s ease-out;">
          <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/5 rounded-full blur-3xl"></div>
          <div class="relative z-10">
            <div class="flex items-center justify-between p-6 border-b ${isDark ? 'border-slate-700/50' : 'border-slate-200'}">
              <h3 class="text-xl font-bold ${isDark ? 'text-white' : 'text-slate-900'}">${title}</h3>
              <button type="button" class="modal-cancel transition-colors ${isDark ? 'text-slate-400 hover:text-white' : 'text-slate-500 hover:text-slate-900'}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
            <div class="p-6 ${isDark ? 'text-slate-300' : 'text-slate-700'}">${body}</div>
            <div class="p-6 border-t ${isDark ? 'border-slate-700/50' : 'border-slate-200'} flex justify-end gap-3">
              <button type="button" class="btn-secondary modal-cancel-btn px-5 py-2.5">取消</button>
              ${confirmText ? `<button type="button" class="btn-primary modal-confirm px-5 py-2.5 shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40">${confirmText}</button>` : ''}
            </div>
          </div>
        </div>
      </div>
    `;
    const close = () => {
      const instance = root.querySelector('#modal-instance');
      if (instance) {
        instance.style.animation = 'fadeOut 0.15s ease-in';
        setTimeout(() => { root.innerHTML = ''; }, 150);
      } else {
        root.innerHTML = '';
      }
    };
    root.querySelector('.modal-cancel')?.addEventListener('click', close);
    root.querySelector('.modal-cancel-btn')?.addEventListener('click', close);
    root.querySelector('.modal-confirm')?.addEventListener('click', () => {
      if (onConfirm) onConfirm();
      close();
    });
    root.querySelector('#modal-instance')?.addEventListener('click', (e) => {
      if (e.target.id === 'modal-instance') close();
    });
    return { close };
  },
  confirm(message, onConfirm) {
    const isDark = document.documentElement.classList.contains('dark');
    return this.open({ title: '确认操作', body: `<p class="${isDark ? 'text-slate-300' : 'text-slate-700'}">${message}</p>`, confirmText: '确定', onConfirm });
  }
};

// ==================== 表格 + 分页 + 筛选 ====================
window.CrudPage = function(config) {
  const { apiPath, columns, idField = 'id', batchActions = [], afterLoad } = config;
  let page = 1, perPage = 20, total = 0, totalPages = 1, list = [], filters = {};

  const container = document.getElementById(config.container || 'crud-container');
  if (!container) return;

  function renderTable() {
    const isDark = document.documentElement.classList.contains('dark');
    const checked = [...container.querySelectorAll('.row-check:checked')].map(c => c.value);
    const thead = columns.map(c => `<th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider ${isDark ? 'text-slate-400' : 'text-slate-600'}">${c.label}</th>`).join('');
    const rows = list.map((row, idx) => {
      const cells = columns.map(c => {
        let val = row[c.key] ?? '';
        if (c.render) val = c.render(val, row);
        return `<td class="px-5 py-4 ${isDark ? 'text-slate-300' : 'text-slate-700'}">${val}</td>`;
      }).join('');
      const checkedAttr = checked.includes(String(row[idField])) ? 'checked' : '';
      return `
        <tr class="transition-colors group ${isDark ? 'border-b border-slate-800/50 hover:bg-slate-800/30' : 'border-b border-slate-200 hover:bg-slate-50'}">
          <td class="px-5 py-4 w-10">
            <input type="checkbox" class="row-check rounded text-emerald-500 cursor-pointer ${isDark ? 'border-slate-600 bg-slate-800/50 focus:ring-emerald-500/30' : 'border-slate-300 bg-white focus:ring-emerald-500/20'}" value="${row[idField]}" ${checkedAttr}>
          </td>
          ${cells}
          <td class="px-5 py-4 text-right opacity-0 group-hover:opacity-100 transition-opacity">
            ${config.rowActions ? config.rowActions(row) : ''}
          </td>
        </tr>
      `;
    }).join('');
    return `
      <div class="overflow-x-auto rounded-xl border ${isDark ? 'border-slate-800/50' : 'border-slate-200'}">
        <table class="w-full text-sm">
          <thead class="backdrop-blur-sm ${isDark ? 'bg-slate-850/50' : 'bg-slate-50'}">
            <tr class="${isDark ? 'border-b border-slate-700/50' : 'border-b border-slate-200'}">
              <th class="w-10 px-5 py-4">
                <input type="checkbox" class="select-all rounded text-emerald-500 cursor-pointer ${isDark ? 'border-slate-600 bg-slate-800/50 focus:ring-emerald-500/30' : 'border-slate-300 bg-white focus:ring-emerald-500/20'}">
              </th>
              ${thead}
              <th class="w-32 px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider ${isDark ? 'text-slate-400' : 'text-slate-600'}">操作</th>
            </tr>
          </thead>
          <tbody class="${isDark ? 'divide-y divide-slate-800/50' : 'divide-y divide-slate-200'}">
            ${rows || `<tr><td colspan="100" class="text-center py-16"><div class="flex flex-col items-center gap-3"><svg class="w-12 h-12 ${isDark ? 'text-slate-600' : 'text-slate-400'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg><p class="${isDark ? 'text-slate-500' : 'text-slate-400'} text-base">暂无数据</p></div></td></tr>`}
          </tbody>
        </table>
      </div>
    `;
  }

  function renderPagination() {
    const isDark = document.documentElement.classList.contains('dark');
    return `
      <div class="flex items-center justify-between mt-6 px-2">
        <div class="flex items-center gap-2">
          <div class="text-sm ${isDark ? 'text-slate-400' : 'text-slate-500'}">
            共 <span class="font-semibold ${isDark ? 'text-white' : 'text-slate-900'}">${total}</span> 条记录
          </div>
          <div class="w-px h-4 ${isDark ? 'bg-slate-700/50' : 'bg-slate-300'}"></div>
          <div class="text-sm ${isDark ? 'text-slate-500' : 'text-slate-400'}">
            第 ${page} / ${totalPages || 1} 页
          </div>
        </div>
        <div class="flex items-center gap-2">
          <button type="button" class="pagination-prev inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all disabled:opacity-50 disabled:cursor-not-allowed ${isDark ? 'text-slate-400 hover:text-white hover:bg-slate-800/60 disabled:hover:bg-transparent disabled:hover:text-slate-400' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 disabled:hover:bg-transparent disabled:hover:text-slate-600'}" ${page <= 1 ? 'disabled' : ''}>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span>上一页</span>
          </button>
          <button type="button" class="pagination-next inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all disabled:opacity-50 disabled:cursor-not-allowed ${isDark ? 'text-slate-400 hover:text-white hover:bg-slate-800/60 disabled:hover:bg-transparent disabled:hover:text-slate-400' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 disabled:hover:bg-transparent disabled:hover:text-slate-600'}" ${page >= totalPages ? 'disabled' : ''}>
            <span>下一页</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </button>
        </div>
      </div>
    `;
  }

  function renderFilters() {
    const isDark = document.documentElement.classList.contains('dark');
    const inputClass = isDark ? 'bg-slate-800/30 border-slate-700/50 focus:bg-slate-800/50' : 'bg-white border-slate-200 focus:bg-slate-50';
    const formHtml = config.filters ? config.filters.map(f => {
      const val = filters[f.key] || '';
      if (f.type === 'select' && Array.isArray(f.options)) {
        const opts = f.options.map(o => { const v = o.value !== undefined ? o.value : o.id; const l = o.label !== undefined ? o.label : o.name; return `<option value="${String(v)}" ${val === String(v) ? 'selected' : ''}>${l ?? v}</option>`; }).join('');
        return `<div class="relative flex-1 min-w-[200px]"><select name="${f.key}" class="input w-full ${inputClass}">${f.placeholder ? `<option value="">${f.placeholder}</option>` : '<option value="">全部</option>'}${opts}</select></div>`;
      }
      return `<div class="relative flex-1 min-w-[240px]"><div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><svg class="w-5 h-5 ${isDark ? 'text-slate-500' : 'text-slate-400'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></div><input type="${f.type || 'text'}" name="${f.key}" placeholder="${f.placeholder || ''}" class="input !pl-10 w-full ${inputClass}" value="${(val + '').replace(/"/g, '&quot;')}"></div>`;
    }).join('') : '';
    return formHtml ? `
      <form class="search-form flex flex-wrap gap-3 mb-6">
        ${formHtml}
        <button type="submit" class="btn-primary px-6 shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
          <span>搜索</span>
        </button>
        <button type="button" class="btn-secondary clear-filters px-6 flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
          <span>清空</span>
        </button>
      </form>
    ` : '';
  }

  const batchActionLabels = { delete: '批量删除', enable: '批量启用', disable: '批量禁用' };
  const batchActionStyles = {
    delete: 'text-red-400 hover:text-red-300 hover:bg-red-500/10 border-red-500/20 hover:border-red-500/30',
    enable: 'text-emerald-400 hover:text-emerald-300 hover:bg-emerald-500/10 border-emerald-500/20 hover:border-emerald-500/30',
    disable: 'text-slate-400 hover:text-slate-300 hover:bg-slate-700/30 border-slate-600/30 hover:border-slate-600/50',
  };
  function renderBatchActions() {
    if (!batchActions.length) return '';
    const btns = batchActions.map((action) => {
      const label = batchActionLabels[action] || action;
      const style = batchActionStyles[action] || batchActionStyles.disable;
      const icon = action === 'delete' ? 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16' : action === 'enable' ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636';
      return `<button type="button" class="batch-btn px-4 py-2 rounded-lg text-sm font-medium border transition-all flex items-center gap-2 ${style}" data-action="${action}"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${icon}"></path></svg><span>${label}</span></button>`;
    }).join('');
    return `<div class="flex gap-2 mb-4">${btns}</div>`;
  }

  async function load() {
    const params = new URLSearchParams({ page, per_page: perPage, ...filters });
    const res = await api(apiPath + '?' + params);
    const d = res.data || res;
    list = d.list || [];
    total = d.total || 0;
    totalPages = d.total_pages || Math.ceil(total / perPage) || 1;
    if (afterLoad) afterLoad(d);
    render();
  }

  function render() {
    container.innerHTML = renderFilters() + renderBatchActions() + renderTable() + renderPagination();
    bindEvents();
  }

  function bindEvents() {
    container.querySelector('.search-form')?.addEventListener('submit', (e) => {
      e.preventDefault();
      const fd = new FormData(e.target);
      filters = Object.fromEntries([...fd].filter(([_, v]) => v));
      page = 1;
      load();
    });
    container.querySelector('.clear-filters')?.addEventListener('click', () => {
      filters = {};
      container.querySelectorAll('.input').forEach(i => { i.value = ''; });
      container.querySelectorAll('.search-form select').forEach(s => { s.value = ''; });
      page = 1;
      load();
    });
    container.querySelector('.select-all')?.addEventListener('change', (e) => {
      container.querySelectorAll('.row-check').forEach(c => c.checked = e.target.checked);
    });
    container.querySelector('.pagination-prev')?.addEventListener('click', () => { if (page > 1) { page--; load(); } });
    container.querySelector('.pagination-next')?.addEventListener('click', () => { if (page < totalPages) { page++; load(); } });
    container.querySelectorAll('.batch-btn').forEach(btn => {
      btn.addEventListener('click', async () => {
        const ids = [...container.querySelectorAll('.row-check:checked')].map(c => c.value).filter(Boolean);
        if (!ids.length) { Modal.open({ title: '提示', body: '请先选择记录' }); return; }
        const action = btn.dataset.action;
        Modal.confirm(`确定要批量${action === 'delete' ? '删除' : action === 'enable' ? '启用' : '禁用'}选中的 ${ids.length} 条记录？`, async () => {
          await api(apiPath.replace(/\/$/, '') + '/batch', {
            method: 'POST',
            body: JSON.stringify({ action, ids }),
          });
          load();
        });
      });
    });
    if (config.bindRowActions) config.bindRowActions(container, load);
  }

  load();
  return { load, getList: () => list };
};
