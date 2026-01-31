<?php
$title = '奖品管理 - 单位端';
$showSidebar = true;
$showHeader = true;
$pageTitle = '奖品管理';
$baseUrl = '/unit';
$siteName = '单位端';
$navItems = unit_nav_items('/unit/prizes');
$content = <<<'HTML'
<div class="mb-6">
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-2xl font-bold mb-2 transition-colors dark:text-white light:text-slate-900">奖品管理</h2>
      <p class="text-sm transition-colors dark:text-slate-400 light:text-slate-500">配置积分兑换奖品</p>
    </div>
    <button type="button" id="btn-add-prize" class="btn-primary px-6 py-3 shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 flex items-center gap-2">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
      <span>新增奖品</span>
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

  const openPrizeModal = (prize, isEdit) => {
    const d = prize || {};
    Modal.open({
      title: isEdit ? '编辑奖品' : '新增奖品',
      body: `<form id="prize-form">
        <div class="space-y-3">
          <div><label class="block text-sm mb-1 dark:text-slate-400 light:text-slate-600">奖品名称 *</label>
            <input name="name" required class="input w-full" placeholder="如：运动水杯" value="${(d.name||'').replace(/"/g,'&quot;')}">
          </div>
          <div><label class="block text-sm mb-1 dark:text-slate-400 light:text-slate-600">封面图</label>
            <div data-upload="image" data-field="image" class="max-w-xs prize-upload" data-initial="${(d.image||'').replace(/"/g,'&quot;').replace(/&/g,'&amp;')}"></div>
          </div>
          <div><label class="block text-sm mb-1 dark:text-slate-400 light:text-slate-600">轮播图</label>
            <p class="text-xs dark:text-slate-500 light:text-slate-600 mb-2">详情页轮播展示，可添加多张</p>
            <div id="images-container" class="flex flex-wrap gap-3 items-start">
              <div id="add-carousel-img" class="w-24 h-24 flex-shrink-0 cursor-pointer border-2 border-dashed rounded-lg flex flex-col items-center justify-center gap-1 transition-colors border-slate-400 dark:border-slate-600 hover:border-emerald-500 dark:hover:border-emerald-500/50 hover:bg-emerald-500/5">
                <svg class="w-8 h-8 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span class="text-xs text-slate-500 dark:text-slate-400">添加</span>
              </div>
            </div>
          </div>
          <div><label class="block text-sm mb-1 dark:text-slate-400 light:text-slate-600">描述（富文本）</label>
            <textarea name="description" class="ckeditor-target input w-full" rows="4" placeholder="奖品详情">${(d.description||'').replace(/<\/textarea/gi,'\x3c!--ta--\x3e')}</textarea>
          </div>
          <div><label class="block text-sm mb-1 dark:text-slate-400 light:text-slate-600">所需积分 *</label>
            <input type="number" name="points_required" required min="0" class="input w-full" value="${d.points_required ?? 0}">
          </div>
          <div><label class="block text-sm mb-1 dark:text-slate-400 light:text-slate-600">库存</label>
            <input type="number" name="stock" min="0" class="input w-full" value="${d.stock ?? 0}">
          </div>
          <div><label class="block text-sm mb-1 dark:text-slate-400 light:text-slate-600">每人限兑</label>
            <input type="number" name="per_user_limit" min="0" class="input w-full" placeholder="0=不限" value="${d.per_user_limit ?? 0}">
          </div>
          <div><label class="block text-sm mb-1 dark:text-slate-400 light:text-slate-600">排序</label>
            <input type="number" name="sort" class="input w-full" value="${d.sort ?? 0}">
          </div>
          <div><label class="block text-sm mb-1 dark:text-slate-400 light:text-slate-600">状态</label>
            <select name="status" class="input w-full">
              <option value="1" ${(d.status??1)==1?'selected':''}>上架</option>
              <option value="0" ${d.status==0?'selected':''}>下架</option>
            </select>
          </div>
        </div>
      </form>`,
      confirmText: '保存',
      onConfirm: async () => {
        const form = document.getElementById('prize-form');
        if (!form) return;
        if (typeof CkEditor !== 'undefined') CkEditor.syncAll();
        const fd = new FormData(form);
        const body = Object.fromEntries(fd);
        body.points_required = parseInt(body.points_required, 10) || 0;
        body.stock = parseInt(body.stock, 10) || 0;
        body.per_user_limit = parseInt(body.per_user_limit, 10) || 0;
        body.sort = parseInt(body.sort, 10) || 0;
        body.status = parseInt(body.status, 10) ?? 1;
        body.image = form.querySelector('input[name="image"]')?.value || null;
        const imgItems = form.querySelectorAll('.img-item[data-url]');
        body.images = [...imgItems].map(el => el.dataset.url).filter(Boolean);

        if (isEdit) {
          await api('/prizes/' + d.id, { method: 'PUT', body: JSON.stringify(body) });
        } else {
          await api('/prizes', { method: 'POST', body: JSON.stringify(body) });
        }
        if (typeof crudReload === 'function') crudReload();
      }
    });

    setTimeout(async () => {
      const form = document.getElementById('prize-form');
      const descTa = form?.querySelector('textarea[name="description"]');
      if (descTa) descTa.value = (descTa.value || '').replace(/\x3c!--ta--\x3e/g, '</textarea>');

      let imgs = [];
      if (Array.isArray(d.images)) imgs = d.images;
      else if (d.images) {
        try { imgs = typeof d.images === 'string' ? JSON.parse(d.images || '[]') : d.images; } catch (e) {}
        if (!Array.isArray(imgs)) imgs = [];
      }
      const container = document.getElementById('images-container');
      const addBtn = document.getElementById('add-carousel-img');
      if (container && addBtn) {
        imgs.forEach(url => {
          const div = document.createElement('div');
          div.className = 'img-item relative inline-block flex-shrink-0';
          div.dataset.url = url || '';
          div.innerHTML = '<img src="'+url+'" class="w-20 h-20 object-cover rounded-lg"><button type="button" class="img-remove absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center hover:bg-red-600">×</button>';
          container.insertBefore(div, addBtn);
        });
        const bindImgRemove = () => container.querySelectorAll('.img-remove').forEach(btn => {
          btn.onclick = () => btn.closest('.img-item')?.remove();
        });
        bindImgRemove();

        addBtn.addEventListener('click', () => {
          const input = document.createElement('input');
          input.type = 'file';
          input.accept = 'image/*';
          input.className = 'hidden';
          input.onchange = async (e) => {
            const file = e.target.files?.[0];
            if (!file) return;
            const fd = new FormData(); fd.append('file', file);
            try {
              const res = await apiFormData('/upload/image', fd);
              const url = res.data?.url || res.data?.path;
              if (url) {
                const div = document.createElement('div');
                div.className = 'img-item relative inline-block flex-shrink-0';
                div.dataset.url = url;
                div.innerHTML = '<img src="'+url+'" class="w-20 h-20 object-cover rounded-lg"><button type="button" class="img-remove absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center hover:bg-red-600">×</button>';
                container.insertBefore(div, addBtn);
                bindImgRemove();
              }
            } catch (err) { Modal.open({ title: '上传失败', body: err.message }); }
          };
          input.click();
        });
      }

      const uploadEl = document.querySelector('.prize-upload');
      if (uploadEl && !uploadEl.dataset.uploadInited) {
        Upload.setup(uploadEl);
        const initialUrl = uploadEl.dataset.initial || d.image || '';
        if (initialUrl) {
          const hiddenInput = form?.querySelector('input[name="image"]');
          if (hiddenInput) hiddenInput.value = initialUrl;
          const preview = uploadEl.querySelector('.upload-preview');
          if (preview) preview.innerHTML = '<img src="' + initialUrl + '" class="max-h-24 rounded">';
        }
        uploadEl.addEventListener('uploaded', (e) => {
          const h = document.getElementById('prize-form')?.querySelector('input[name="image"]');
          if (h) h.value = e.detail.url || '';
        });
      }
      if (typeof CkEditor !== 'undefined') {
        await CkEditor.init('#prize-form textarea.ckeditor-target', { height: 200 });
      }
    }, 50);
  };

  let crudReload;
  CrudPage({
    container: 'crud-container',
    apiPath: '/prizes',
    columns: [
      { key: 'id', label: 'ID' },
      { key: 'name', label: '奖品名称' },
      { key: 'image', label: '封面', render: (v) => v ? '<img src="'+v+'" class="w-12 h-12 object-cover rounded">' : '-' },
      { key: 'images', label: '轮播图', render: (v) => (Array.isArray(v) && v.length) ? v.length + ' 张' : '-' },
      { key: 'points_required', label: '所需积分' },
      { key: 'stock', label: '库存' },
      { key: 'per_user_limit', label: '每人限兑', render: (v) => v ? v : '不限' },
      { key: 'sort', label: '排序' },
      { key: 'status', label: '状态', render: (v) => v === 1 ? '<span class="text-emerald-500">上架</span>' : '<span class="text-slate-500">下架</span>' },
      { key: 'created_at', label: '创建时间', render: (v) => formatDateTime(v) },
    ],
    filters: [
      { key: 'keyword', placeholder: '奖品名称' },
      { key: 'status', type: 'select', placeholder: '状态', options: [
        { value: '1', label: '上架' }, { value: '0', label: '下架' }
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
          Modal.confirm('确定删除该奖品？', async () => {
            await api('/prizes/' + id, { method: 'DELETE' });
            reload();
          });
        }
        if (editBtn) {
          const id = editBtn.dataset.id;
          const res = await api('/prizes/' + id);
          openPrizeModal(res.data, true);
        }
      });
    },
  });

  document.getElementById('btn-add-prize').addEventListener('click', () => openPrizeModal(null, false));
});
</script>
JS;
include __DIR__ . '/../layouts/app.php';
