<?php
$title = '用户管理';
$showSidebar = true;
$showHeader = true;
$pageTitle = '用户管理';
$baseUrl = '/admin';
$siteName = '管理后台';
$navItems = admin_nav_items('/admin/members');
$content = <<<'HTML'
<div class="mb-6">
  <div>
    <h2 class="text-2xl font-bold mb-2 transition-colors dark:text-white light:text-slate-900">用户管理</h2>
    <p class="text-sm transition-colors dark:text-slate-400 light:text-slate-500">小程序端用户，由微信登录自动创建</p>
  </div>
</div>

<div class="rounded-2xl backdrop-blur-xl border shadow-2xl overflow-hidden transition-colors dark:bg-slate-900/60 dark:border-slate-700/50 light:bg-white light:border-slate-200">
  <div id="crud-container" class="p-6">
    <div class="flex items-center justify-center py-12">
      <div class="text-center">
        <div class="w-12 h-12 mx-auto mb-4 rounded-xl flex items-center justify-center transition-colors dark:bg-slate-800/50 light:bg-slate-100">
          <svg class="w-6 h-6 animate-spin transition-colors dark:text-slate-500 light:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
        </div>
        <p class="transition-colors dark:text-slate-500 light:text-slate-400">加载中...</p>
      </div>
    </div>
  </div>
</div>
HTML;
$scripts = <<<'JS'
<script>
document.addEventListener('DOMContentLoaded', async () => {
  if (!localStorage.getItem('admin_token')) { location.href = '/admin/login'; return; }
  window.APP_BASE = '/admin';
  window.APP_API_SUFFIX = '/api';

  let unitsList = [];
  try {
    const r = await fetch(window.APP_BASE + '/api/units?per_page=500', { headers: { 'Authorization': 'Bearer ' + localStorage.getItem('admin_token') } });
    const d = await r.json();
    unitsList = (d.data && d.data.list) ? d.data.list : (d.list || []);
  } catch (e) {}

  const unitOptions = unitsList.map(u => ({ value: u.id, label: u.name || u.account || 'ID:' + u.id }));

  CrudPage({
    container: 'crud-container',
    apiPath: '/members',
    columns: [
      { key: 'id', label: 'ID' },
      { key: 'nickname', label: '昵称' },
      { key: 'phone', label: '手机号' },
      { key: 'unit', label: '所属单位', render: (v, row) => (row.unit && row.unit.name) ? row.unit.name : '-' },
      { key: 'openid', label: 'OpenID', render: (v) => v ? (String(v).slice(0, 12) + '…') : '-' },
      { key: 'created_at', label: '注册时间' },
    ],
    filters: [
      { key: 'keyword', placeholder: '昵称/手机号/OpenID' },
      { key: 'phone', placeholder: '手机号' },
      { key: 'unit_id', type: 'select', placeholder: '全部单位', options: unitOptions },
    ],
    batchActions: ['delete'],
    rowActions: (row) => `
      <button type="button" class="row-edit text-emerald-400 hover:text-emerald-300 text-sm mr-2" data-id="${row.id}">编辑</button>
      <button type="button" class="row-delete text-red-400 hover:text-red-300 text-sm" data-id="${row.id}">删除</button>
    `,
    bindRowActions: (container, reload) => {
      container.addEventListener('click', (e) => {
        if (e.target.classList.contains('row-delete')) {
          const id = e.target.dataset.id;
          Modal.confirm('确定删除该用户？', async () => {
            await api('/members/' + id, { method: 'DELETE' });
            reload();
          });
        }
        if (e.target.classList.contains('row-edit')) {
          const id = e.target.dataset.id;
          fetchRowAndEdit(id, reload);
        }
      });
    },
  });

  async function fetchRowAndEdit(id, reload) {
    const res = await api('/members/' + id);
    const d = res.data || {};
    const unitId = d.unit_id != null ? String(d.unit_id) : '';
    const unitSelectOpts = '<option value="">无</option>' + unitOptions.map(o => '<option value="' + o.value + '" ' + (unitId === String(o.value) ? 'selected' : '') + '>' + (o.label || '').replace(/</g, '&lt;') + '</option>').join('');
    Modal.open({
      title: '编辑用户',
      body: '<form id="edit-form"><div class="space-y-3"><input name="nickname" placeholder="昵称" class="input" value="' + (d.nickname || '').replace(/"/g, '&quot;') + '"><input name="phone" placeholder="手机号" class="input" value="' + (d.phone || '').replace(/"/g, '&quot;') + '"><label class="block text-sm ' + (document.documentElement.classList.contains('dark') ? 'text-slate-400' : 'text-slate-600') + '">所属单位</label><select name="unit_id" class="input w-full">' + unitSelectOpts + '</select></div></form>',
      confirmText: '保存',
      onConfirm: async () => {
        const fd = new FormData(document.getElementById('edit-form'));
        const body = Object.fromEntries(fd);
        if (body.unit_id === '') body.unit_id = null; else body.unit_id = parseInt(body.unit_id, 10);
        await api('/members/' + id, { method: 'PUT', body: JSON.stringify(body) });
        reload();
      }
    });
  }
});
</script>
JS;
include __DIR__ . '/../layouts/app.php';
