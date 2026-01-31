<?php
$title = '审核打卡 - 单位端';
$showSidebar = true;
$showHeader = true;
$pageTitle = '审核打卡';
$baseUrl = '/unit';
$siteName = '单位端';
$navItems = unit_nav_items('/unit/checkins');
$content = <<<'HTML'
<div class="mb-6">
  <div>
    <h2 class="text-2xl font-bold mb-2 transition-colors dark:text-white light:text-slate-900">审核打卡</h2>
    <p class="text-sm transition-colors dark:text-slate-400 light:text-slate-500">审核需要人工确认的打卡记录</p>
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

  let tasksList = [];
  try {
    const tr = await api('/tasks?per_page=500');
    tasksList = (tr.data && tr.data.list) ? tr.data.list : (tr.list || []);
  } catch (e) {}
  const taskOptions = tasksList.map(t => ({ value: t.id, label: t.name }));

  const statusLabels = { 0: '待审核', 1: '已通过', 2: '已拒绝' };
  const statusClass = { 0: 'text-amber-500', 1: 'text-emerald-500', 2: 'text-red-500' };

  CrudPage({
    container: 'crud-container',
    apiPath: '/checkins',
    columns: [
      { key: 'id', label: 'ID' },
      { key: 'user', label: '用户', render: (v, row) => (row.user && row.user.nickname) ? row.user.nickname : '-' },
      { key: 'task', label: '任务', render: (v, row) => (row.task && row.task.name) ? row.task.name : '-' },
      { key: 'value', label: '打卡数据', render: (v) => v ? (typeof v === 'object' ? JSON.stringify(v) : v) : '-' },
      { key: 'media_url', label: '媒体', render: (v) => {
        if (!v) return '-';
        const ext = (v.split('.').pop() || '').toLowerCase();
        const isImg = /jpg|jpeg|png|gif|webp/.test(ext);
        return isImg ? '<a href="'+v+'" target="_blank" class="text-emerald-500 hover:underline">查看图片</a>' : '<a href="'+v+'" target="_blank" class="text-emerald-500 hover:underline">查看</a>';
      }},
      { key: 'points', label: '积分' },
      { key: 'status', label: '状态', render: (v) => '<span class="' + (statusClass[v] || '') + '">' + (statusLabels[v] || '-') + '</span>' },
      { key: 'checked_at', label: '打卡时间', render: (v) => formatDateTime(v) },
      { key: 'created_at', label: '提交时间', render: (v) => formatDateTime(v) },
    ],
    filters: [
      { key: 'status', type: 'select', placeholder: '状态', options: [
        { value: '0', label: '待审核' }, { value: '1', label: '已通过' }, { value: '2', label: '已拒绝' }
      ]},
      { key: 'task_id', type: 'select', placeholder: '任务', options: taskOptions },
    ],
    rowActions: (row) => {
      const actions = [];
      if (row.status === 0) {
        actions.push(`<button type="button" class="crud-row-btn row-approve text-emerald-500" data-id="${row.id}"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span>通过</span></button>`);
        actions.push(`<button type="button" class="crud-row-btn row-reject text-red-500" data-id="${row.id}"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg><span>拒绝</span></button>`);
      }
      actions.push(`<button type="button" class="crud-row-btn row-detail" data-id="${row.id}"><span>详情</span></button>`);
      return RowActions.wrap(actions);
    },
    bindRowActions: (container, reload) => {
      container.addEventListener('click', async (e) => {
        const approveBtn = e.target.closest('.row-approve');
        const rejectBtn = e.target.closest('.row-reject');
        const detailBtn = e.target.closest('.row-detail');
        if (approveBtn) {
          const id = approveBtn.dataset.id;
          Modal.confirm('确定通过该打卡？', async () => {
            await api('/checkins/' + id + '/approve', { method: 'POST' });
            reload();
          });
        }
        if (rejectBtn) {
          const id = rejectBtn.dataset.id;
          Modal.confirm('确定拒绝该打卡？', async () => {
            await api('/checkins/' + id + '/reject', { method: 'POST' });
            reload();
          });
        }
        if (detailBtn) {
          const id = detailBtn.dataset.id;
          const res = await api('/checkins/' + id);
          const d = res.data || {};
          const userInfo = d.user ? (d.user.nickname || d.user.phone || 'ID:' + d.user.id) : '-';
          const taskInfo = d.task ? d.task.name : '-';
          const valueStr = d.value ? (typeof d.value === 'object' ? JSON.stringify(d.value, null, 2) : d.value) : '-';
          const mediaHtml = d.media_url ? '<a href="' + d.media_url + '" target="_blank" class="text-emerald-500">查看媒体</a>' : '-';
          Modal.open({
            title: '打卡详情',
            body: `<div class="space-y-2 text-sm">
              <p><span class="dark:text-slate-500 light:text-slate-600">用户：</span>${userInfo}</p>
              <p><span class="dark:text-slate-500 light:text-slate-600">任务：</span>${taskInfo}</p>
              <p><span class="dark:text-slate-500 light:text-slate-600">打卡数据：</span><pre class="mt-1 p-2 rounded bg-slate-100 dark:bg-slate-800 text-xs overflow-auto">${valueStr.replace(/</g,'&lt;')}</pre></p>
              <p><span class="dark:text-slate-500 light:text-slate-600">媒体：</span>${mediaHtml}</p>
              <p><span class="dark:text-slate-500 light:text-slate-600">积分：</span>${d.points || 0}</p>
              <p><span class="dark:text-slate-500 light:text-slate-600">打卡时间：</span>${formatDateTime(d.checked_at)}</p>
            </div>`,
            confirmText: '关闭'
          });
        }
      });
    },
  });
});
</script>
JS;
include __DIR__ . '/../layouts/app.php';
