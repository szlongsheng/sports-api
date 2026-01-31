<?php
$title = '用户管理 - 单位端';
$showSidebar = true;
$showHeader = true;
$pageTitle = '用户管理';
$baseUrl = '/unit';
$siteName = '单位端';
$navItems = unit_nav_items('/unit/users');
$content = <<<'HTML'
<div class="mb-6">
  <div>
    <h2 class="text-2xl font-bold mb-2 transition-colors dark:text-white light:text-slate-900">用户管理</h2>
    <p class="text-sm transition-colors dark:text-slate-400 light:text-slate-500">本单位下的小程序用户</p>
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
  if (!localStorage.getItem('unit_token')) { location.href = '/unit/login'; return; }
  window.APP_BASE = '/unit';
  window.APP_API_SUFFIX = '/api';

  CrudPage({
    container: 'crud-container',
    apiPath: '/users',
    columns: [
      { key: 'id', label: 'ID' },
      { key: 'nickname', label: '昵称', render: (v) => v || '-' },
      { key: 'phone', label: '手机号', render: (v) => v || '-' },
      { key: 'avatar', label: '头像', render: (v) => v ? '<img src="' + v + '" class="w-10 h-10 rounded-full object-cover">' : '-' },
      { key: 'created_at', label: '注册时间', render: (v) => formatDateTime(v) },
    ],
    filters: [
      { key: 'keyword', placeholder: '昵称/手机号/OpenID' },
      { key: 'phone', placeholder: '手机号' },
    ],
    rowActions: (row) => RowActions.wrap([RowActions.edit(row)]),
    bindRowActions: (container, reload) => {
      container.addEventListener('click', (e) => {
        const editBtn = e.target.closest('.row-edit');
        if (editBtn) {
          const id = editBtn.dataset.id;
          fetchRowAndEdit(id, reload);
        }
      });
    },
  });

  async function fetchRowAndEdit(id, reload) {
    const res = await api('/users/' + id);
    const d = res.data || {};
    Modal.open({
      title: '编辑用户',
      body: '<form id="edit-form"><div class="space-y-3"><input name="nickname" placeholder="昵称" class="input" value="' + (d.nickname || '').replace(/"/g, '&quot;') + '"><input name="phone" placeholder="手机号" class="input" value="' + (d.phone || '').replace(/"/g, '&quot;') + '"></div></form>',
      confirmText: '保存',
      onConfirm: async () => {
        const fd = new FormData(document.getElementById('edit-form'));
        const body = Object.fromEntries(fd);
        await api('/users/' + id, { method: 'PUT', body: JSON.stringify(body) });
        reload();
      }
    });
  }
});
</script>
JS;
include __DIR__ . '/../layouts/app.php';
