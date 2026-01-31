<?php
$title = '单位管理';
$showSidebar = true;
$showHeader = true;
$pageTitle = '单位管理';
$baseUrl = '/admin';
$siteName = '管理后台';
$navItems = [
  ['label' => '仪表盘', 'url' => '/admin/dashboard', 'active' => false],
  ['label' => '管理员', 'url' => '/admin/users', 'active' => false],
  ['label' => '用户管理', 'url' => '/admin/members', 'active' => false],
  ['label' => '单位管理', 'url' => '/admin/units', 'active' => true],
];
$content = <<<'HTML'
<div class="mb-6">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h2 class="text-2xl font-bold mb-2 transition-colors dark:text-white light:text-slate-900">单位管理</h2>
      <p class="text-sm transition-colors dark:text-slate-400 light:text-slate-500">赛事参与单位/机构，如学校、俱乐部等</p>
    </div>
    <button type="button" class="btn-primary flex items-center gap-2 px-6 py-3 shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 hover:scale-105 transition-all" id="btn-add">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
      </svg>
      <span>新增单位</span>
    </button>
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
document.addEventListener('DOMContentLoaded', () => {
  if (!localStorage.getItem('admin_token')) { location.href = '/admin/login'; return; }
  window.APP_BASE = '/admin';
  window.APP_API_SUFFIX = '/api';

  CrudPage({
    container: 'crud-container',
    apiPath: '/units',
    columns: [
      { key: 'id', label: 'ID' },
      { key: 'account', label: '登录账号' },
      { key: 'name', label: '单位名称' },
      { key: 'contact', label: '联系人' },
      { key: 'phone', label: '联系电话' },
      { key: 'status', label: '状态', render: (v) => v === 1 ? '<span class="text-emerald-400">启用</span>' : '<span class="text-slate-500">禁用</span>' },
      { key: 'created_at', label: '创建时间' },
    ],
    filters: [
      { key: 'keyword', placeholder: '搜索账号/名称/联系人/电话' },
    ],
    batchActions: ['delete', 'enable', 'disable'],
    rowActions: (row) => `
      <button type="button" class="row-edit text-emerald-400 hover:text-emerald-300 text-sm mr-2" data-id="${row.id}">编辑</button>
      <button type="button" class="row-delete text-red-400 hover:text-red-300 text-sm" data-id="${row.id}">删除</button>
    `,
    bindRowActions: (container, reload) => {
      container.addEventListener('click', (e) => {
        if (e.target.classList.contains('row-delete')) {
          const id = e.target.dataset.id;
          Modal.confirm('确定删除该单位？', async () => {
            await api('/units/' + id, { method: 'DELETE' });
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
    const res = await api('/units/' + id);
    const d = res.data || {};
    Modal.open({
      title: '编辑单位',
      body: `<form id="edit-form"><div class="space-y-3"><input name="account" placeholder="登录账号" class="input" value="${(d.account || '').replace(/"/g, '&quot;')}" required><input name="name" placeholder="单位名称" class="input" value="${(d.name || '').replace(/"/g, '&quot;')}" required><input name="contact" placeholder="联系人" class="input" value="${(d.contact || '').replace(/"/g, '&quot;')}"><input name="phone" placeholder="联系电话" class="input" value="${(d.phone || '').replace(/"/g, '&quot;')}"><input name="password" type="password" placeholder="新密码（留空不修改）" class="input"><input name="status" type="number" placeholder="状态 1启用 0禁用" class="input" value="${d.status ?? 1}" min="0" max="1"></div></form>`,
      confirmText: '保存',
      onConfirm: async () => {
        const fd = new FormData(document.getElementById('edit-form'));
        const body = Object.fromEntries(fd);
        if (!body.password) delete body.password;
        body.status = parseInt(body.status, 10) || 1;
        await api('/units/' + id, { method: 'PUT', body: JSON.stringify(body) });
        reload();
      }
    });
  }

  document.getElementById('btn-add')?.addEventListener('click', () => {
    Modal.open({
      title: '新增单位',
      body: '<form id="add-form"><div class="space-y-3"><input name="account" placeholder="登录账号" class="input" required><input name="password" type="password" placeholder="密码" class="input" required><input name="name" placeholder="单位名称" class="input" required><input name="contact" placeholder="联系人" class="input"><input name="phone" placeholder="联系电话" class="input"></div></form>',
      confirmText: '创建',
      onConfirm: async () => {
        const fd = new FormData(document.getElementById('add-form'));
        await api('/units', { method: 'POST', body: JSON.stringify(Object.fromEntries(fd)) });
        location.reload();
      }
    });
  });
});
</script>
JS;
include __DIR__ . '/../layouts/app.php';
