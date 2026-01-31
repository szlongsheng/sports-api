<?php
$title = '打卡任务管理 - 单位端';
$showSidebar = true;
$showHeader = true;
$pageTitle = '打卡任务';
$baseUrl = '/unit';
$siteName = '单位端';
$navItems = unit_nav_items('/unit/tasks');
$content = <<<'HTML'
<div class="mb-6">
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-2xl font-bold mb-2 transition-colors dark:text-white light:text-slate-900">打卡任务</h2>
      <p class="text-sm transition-colors dark:text-slate-400 light:text-slate-500">配置用户可打卡的运动任务</p>
    </div>
    <button type="button" id="btn-add-task" class="btn-primary px-6 py-3 shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 flex items-center gap-2">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
      <span>新增任务</span>
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
document.addEventListener('DOMContentLoaded', async () => {
  if (!localStorage.getItem('unit_token')) { location.href = '/unit/login'; return; }
  window.APP_BASE = '/unit';
  window.APP_API_SUFFIX = '/api';

  const typeLabels = { steps: '步数', weight: '体重', video: '视频', custom: '自定义' };

  const parsePointsRule = (pr) => {
    if (!pr) return { type: 'fixed', points: 5 };
    if (typeof pr === 'string') try { pr = JSON.parse(pr); } catch (e) { return { type: 'fixed', points: 5 }; }
    return pr.type === 'steps' ? pr : { type: 'fixed', points: pr.points || 5 };
  };

  const renderTiersHtml = (tiers) => {
    const arr = (tiers || [{ min: 3000, max: 6000, points: 5 }, { min: 6000, max: 8000, points: 8 }, { min: 8000, max: '', points: 10 }]);
    return arr.map((t, i) => `
      <div class="tier-row flex flex-wrap gap-2 items-center w-full">
        <input type="number" class="tier-min input !w-24 min-w-0" placeholder="起始" value="${t.min || ''}">
        <span class="text-slate-500">-</span>
        <input type="number" class="tier-max input !w-24 min-w-0" placeholder="结束(空=以上)" value="${t.max || ''}">
        <span class="text-slate-500">步得</span>
        <input type="number" class="tier-points input !w-16 min-w-0" placeholder="分" value="${t.points || ''}">
        <button type="button" class="tier-remove p-2 text-red-500 hover:bg-red-500/10 rounded shrink-0">×</button>
      </div>
    `).join('');
  };

  const openTaskModal = (task, isEdit) => {
    const d = task || {};
    const pr = parsePointsRule(d.points_rule);
    const isSteps = pr.type === 'steps';
    const tiersHtml = isSteps ? renderTiersHtml(pr.tiers) : '';
    Modal.open({
      title: isEdit ? '编辑任务' : '新增任务',
      body: `<form id="task-form">
        <div class="space-y-3">
          <div><label class="block text-sm mb-1 dark:text-slate-400 light:text-slate-600">任务类型</label>
            <select name="type" class="input w-full">
              <option value="custom" ${(d.type||'custom')==='custom'?'selected':''}>自定义</option>
              <option value="steps" ${d.type==='steps'?'selected':''}>微信步数</option>
              <option value="weight" ${d.type==='weight'?'selected':''}>体重</option>
              <option value="video" ${d.type==='video'?'selected':''}>视频</option>
            </select>
          </div>
          <div><label class="block text-sm mb-1 dark:text-slate-400 light:text-slate-600">任务名称 *</label>
            <input name="name" required class="input w-full" placeholder="如：跳绳、微信步数" value="${(d.name||'').replace(/"/g,'&quot;')}">
          </div>
          <div><label class="block text-sm mb-1 dark:text-slate-400 light:text-slate-600">描述</label>
            <textarea name="description" class="input w-full" rows="2" placeholder="运动说明">${(d.description||'').replace(/</g,'&lt;')}</textarea>
          </div>
          <div><label class="block text-sm mb-1 dark:text-slate-400 light:text-slate-600">积分规则</label>
            <div class="space-y-2">
              <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" name="pts_type" value="fixed" ${!isSteps?'checked':''}>
                  <span>固定分数</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" name="pts_type" value="steps" ${isSteps?'checked':''}>
                  <span>微信步数阶梯</span>
                </label>
              </div>
              <div id="pts-fixed" class="${isSteps?'hidden':''}">
                <input type="number" name="fixed_points" class="input w-24" min="1" value="${pr.type==='fixed'?(pr.points||5):5}" placeholder="分数">
                <span class="ml-2 text-sm dark:text-slate-500 light:text-slate-600">分/次</span>
              </div>
              <div id="pts-steps" class="${isSteps?'':'hidden'} space-y-2">
                <p class="text-xs dark:text-slate-500 light:text-slate-600">例如：3000-6000步得5分，6000-8000得8分，8000以上得10分</p>
                <div id="tiers-container">${tiersHtml}</div>
                <button type="button" id="add-tier" class="text-sm text-emerald-500 hover:underline">+ 添加一档</button>
              </div>
            </div>
          </div>
          <div><label class="block text-sm mb-1 dark:text-slate-400 light:text-slate-600">需上传媒体(视频/图片)</label>
            <select name="need_media" class="input w-full">
              <option value="0" ${(d.need_media||0)==0?'selected':''}>否</option>
              <option value="1" ${d.need_media==1?'selected':''}>是</option>
            </select>
          </div>
          <div><label class="block text-sm mb-1 dark:text-slate-400 light:text-slate-600">需审核</label>
            <select name="need_review" class="input w-full">
              <option value="0" ${(d.need_review||0)==0?'selected':''}>否</option>
              <option value="1" ${d.need_review==1?'selected':''}>是</option>
            </select>
          </div>
          <div><label class="block text-sm mb-1 dark:text-slate-400 light:text-slate-600">排序</label>
            <input type="number" name="sort" class="input w-full" value="${d.sort||0}">
          </div>
          <div><label class="block text-sm mb-1 dark:text-slate-400 light:text-slate-600">状态</label>
            <select name="status" class="input w-full">
              <option value="1" ${(d.status??1)==1?'selected':''}>启用</option>
              <option value="0" ${d.status==0?'selected':''}>禁用</option>
            </select>
          </div>
        </div>
      </form>`,
      confirmText: '保存',
      onConfirm: async () => {
        const form = document.getElementById('task-form');
        const fd = new FormData(form);
        const body = Object.fromEntries(fd);
        body.need_media = parseInt(body.need_media, 10) || 0;
        body.need_review = parseInt(body.need_review, 10) || 0;
        body.sort = parseInt(body.sort, 10) || 0;
        body.status = parseInt(body.status, 10) ?? 1;

        const ptsType = form.querySelector('input[name="pts_type"]:checked')?.value || 'fixed';
        if (ptsType === 'fixed') {
          body.points_rule = { type: 'fixed', points: parseInt(body.fixed_points, 10) || 5 };
        } else {
          const rows = form.querySelectorAll('.tier-row');
          const tiers = [];
          rows.forEach(r => {
            const min = parseInt(r.querySelector('.tier-min')?.value, 10);
            const maxVal = r.querySelector('.tier-max')?.value;
            const max = maxVal === '' ? null : parseInt(maxVal, 10);
            const points = parseInt(r.querySelector('.tier-points')?.value, 10);
            if (!isNaN(min) && !isNaN(points)) tiers.push({ min, max: max || null, points });
          });
          body.points_rule = { type: 'steps', tiers: tiers.length ? tiers : [{ min: 0, max: 999999, points: 1 }] };
        }
        delete body.pts_type;
        delete body.fixed_points;

        if (isEdit) {
          await api('/tasks/' + d.id, { method: 'PUT', body: JSON.stringify(body) });
        } else {
          await api('/tasks', { method: 'POST', body: JSON.stringify(body) });
        }
        if (typeof crudReload === 'function') crudReload();
      }
    });

    const form = document.getElementById('task-form');
    form?.querySelectorAll('input[name="pts_type"]').forEach(r => {
      r.addEventListener('change', () => {
        const isSteps = form.querySelector('input[name="pts_type"]:checked')?.value === 'steps';
        document.getElementById('pts-fixed').classList.toggle('hidden', isSteps);
        const stepsEl = document.getElementById('pts-steps');
        stepsEl.classList.toggle('hidden', !isSteps);
        if (isSteps && !document.getElementById('tiers-container').querySelector('.tier-row')) {
          document.getElementById('add-tier').click();
        }
      });
    });
    document.getElementById('add-tier')?.addEventListener('click', () => {
      const c = document.getElementById('tiers-container');
      const div = document.createElement('div');
      div.className = 'tier-row flex gap-2 items-center';
      div.innerHTML = '<input type="number" class="tier-min input !w-24 min-w-0" placeholder="起始"><span class="text-slate-500">-</span><input type="number" class="tier-max input !w-24 min-w-0" placeholder="结束(空=以上)"><span class="text-slate-500">步得</span><input type="number" class="tier-points input !w-16 min-w-0" placeholder="分"><button type="button" class="tier-remove p-2 text-red-500 hover:bg-red-500/10 rounded shrink-0">×</button>';
      c.appendChild(div);
      div.querySelector('.tier-remove').addEventListener('click', () => div.remove());
    });
    document.getElementById('tiers-container')?.querySelectorAll('.tier-remove').forEach(btn => {
      btn.addEventListener('click', () => btn.closest('.tier-row')?.remove());
    });
  };

  let crudReload;
  CrudPage({
    container: 'crud-container',
    apiPath: '/tasks',
    columns: [
      { key: 'id', label: 'ID' },
      { key: 'name', label: '任务名称' },
      { key: 'type', label: '类型', render: (v) => typeLabels[v] || v },
      { key: 'points_rule', label: '积分规则', render: (v) => {
        if (!v) return '-';
        const p = typeof v === 'string' ? (()=>{try{return JSON.parse(v)}catch(e){return {}}})() : v;
        if (p.type === 'fixed') return '固定 ' + (p.points||0) + ' 分';
        if (p.type === 'steps' && p.tiers) return '步数阶梯 (' + p.tiers.length + '档)';
        return '-';
      }},
      { key: 'need_media', label: '需媒体', render: (v) => v ? '是' : '否' },
      { key: 'need_review', label: '需审核', render: (v) => v ? '是' : '否' },
      { key: 'sort', label: '排序' },
      { key: 'status', label: '状态', render: (v) => v === 1 ? '<span class="text-emerald-500">启用</span>' : '<span class="text-slate-500">禁用</span>' },
      { key: 'created_at', label: '创建时间', render: (v) => formatDateTime(v) },
    ],
    filters: [
      { key: 'keyword', placeholder: '任务名称' },
      { key: 'type', type: 'select', placeholder: '类型', options: [
        { value: 'custom', label: '自定义' }, { value: 'steps', label: '步数' },
        { value: 'weight', label: '体重' }, { value: 'video', label: '视频' }
      ]},
      { key: 'status', type: 'select', placeholder: '状态', options: [
        { value: '1', label: '启用' }, { value: '0', label: '禁用' }
      ]},
    ],
    rowActions: (row) => RowActions.wrap([RowActions.edit(row), RowActions.delete(row)]),
    bindRowActions: (container, reload) => {
      crudReload = reload;
      container.addEventListener('click', async (e) => {
        const deleteBtn = e.target.closest('.row-delete');
        const editBtn = e.target.closest('.row-edit');
        if (deleteBtn) {
          const id = deleteBtn.dataset.id;
          Modal.confirm('确定删除该任务？', async () => {
            await api('/tasks/' + id, { method: 'DELETE' });
            reload();
          });
        }
        if (editBtn) {
          const id = editBtn.dataset.id;
          const res = await api('/tasks/' + id);
          openTaskModal(res.data, true);
        }
      });
    },
  });

  document.getElementById('btn-add-task').addEventListener('click', () => openTaskModal(null, false));
});
</script>
JS;
include __DIR__ . '/../layouts/app.php';
