<?php
$title = '活动设置 - 单位端';
$showSidebar = true;
$showHeader = true;
$pageTitle = '活动设置';
$baseUrl = '/unit';
$siteName = '单位端';
$navItems = unit_nav_items('/unit/campaign');
$content = <<<'HTML'
<div class="mb-6">
  <h2 class="text-2xl font-bold mb-2 transition-colors dark:text-white light:text-slate-900">活动设置</h2>
  <p class="text-sm transition-colors dark:text-slate-400 light:text-slate-500">配置本单位的减脂计划活动</p>
</div>

<form id="campaign-form" class="space-y-6">
  <!-- 基本信息 -->
  <div class="rounded-2xl backdrop-blur-xl border p-6 transition-colors dark:bg-slate-900/60 dark:border-slate-700/50 light:bg-white light:border-slate-200">
    <h3 class="text-lg font-semibold mb-4 transition-colors dark:text-white light:text-slate-900">基本信息</h3>
    <div class="grid gap-4 md:grid-cols-2">
      <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-2 transition-colors dark:text-slate-300 light:text-slate-700">活动名称 *</label>
        <input type="text" name="name" required class="input w-full" placeholder="如：2025春季减脂计划">
      </div>
      <div>
        <label class="block text-sm font-medium mb-2 transition-colors dark:text-slate-300 light:text-slate-700">开始时间</label>
        <input type="datetime-local" name="start_at" class="input w-full">
      </div>
      <div>
        <label class="block text-sm font-medium mb-2 transition-colors dark:text-slate-300 light:text-slate-700">结束时间</label>
        <input type="datetime-local" name="end_at" class="input w-full">
      </div>
      <div>
        <label class="block text-sm font-medium mb-2 transition-colors dark:text-slate-300 light:text-slate-700">邀请码</label>
        <input type="text" name="invite_code" class="input w-full" placeholder="用户加入时输入">
      </div>
      <div>
        <label class="block text-sm font-medium mb-2 transition-colors dark:text-slate-300 light:text-slate-700">积分上浮系数</label>
        <input type="number" name="points_multiplier" class="input w-full" value="1" step="0.01" min="0.1" placeholder="兑换时 实际所需=基础积分×系数">
      </div>
      <div>
        <label class="block text-sm font-medium mb-2 transition-colors dark:text-slate-300 light:text-slate-700">兑换审核</label>
        <select name="exchange_review" class="input w-full">
          <option value="1">需要审核</option>
          <option value="0">无需审核</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium mb-2 transition-colors dark:text-slate-300 light:text-slate-700">状态</label>
        <select name="status" class="input w-full">
          <option value="1">启用</option>
          <option value="0">禁用</option>
        </select>
      </div>
    </div>
  </div>

  <!-- 启动页 -->
  <div class="rounded-2xl backdrop-blur-xl border p-6 transition-colors dark:bg-slate-900/60 dark:border-slate-700/50 light:bg-white light:border-slate-200">
    <h3 class="text-lg font-semibold mb-4 transition-colors dark:text-white light:text-slate-900">启动页背景图</h3>
    <div data-upload="image" data-field="splash_image" class="max-w-xs"></div>
  </div>

  <!-- 活动详情 -->
  <div class="rounded-2xl backdrop-blur-xl border p-6 transition-colors dark:bg-slate-900/60 dark:border-slate-700/50 light:bg-white light:border-slate-200">
    <h3 class="text-lg font-semibold mb-4 transition-colors dark:text-white light:text-slate-900">活动详情（富文本）</h3>
    <textarea name="detail_content" rows="6" class="ckeditor-target input w-full" placeholder="支持富文本编辑，可图文混排"></textarea>
  </div>

  <!-- 活动规则 -->
  <div class="rounded-2xl backdrop-blur-xl border p-6 transition-colors dark:bg-slate-900/60 dark:border-slate-700/50 light:bg-white light:border-slate-200">
    <h3 class="text-lg font-semibold mb-4 transition-colors dark:text-white light:text-slate-900">活动规则（富文本）</h3>
    <textarea name="rules_content" rows="6" class="ckeditor-target input w-full" placeholder="支持富文本编辑"></textarea>
  </div>

  <div class="flex gap-4">
    <button type="submit" class="btn-primary px-8 py-3 shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40">保存</button>
  </div>
</form>

<p id="form-msg" class="mt-4 text-sm hidden"></p>
HTML;
$scripts = <<<'JS'
<script>
document.addEventListener('DOMContentLoaded', async () => {
  if (!localStorage.getItem('unit_token')) { location.href = '/unit/login'; return; }
  window.APP_BASE = '/unit';
  window.APP_API_SUFFIX = '/api';

  const form = document.getElementById('campaign-form');
  const msgEl = document.getElementById('form-msg');

  // 初始化 CKEditor（在加载数据之后调用）
  const initEditors = () => CkEditor.init('textarea.ckeditor-target');

  // 上传组件：同步到 hidden input
  form.querySelector('[data-upload]')?.addEventListener('uploaded', (e) => {
    const hiddenInput = form.querySelector('input[name="splash_image"]');
    if (hiddenInput) hiddenInput.value = e.detail.url || '';
  });

  // 加载已有活动
  try {
    const res = await api('/campaign');
    const d = res.data;
    if (d) {
      form.querySelector('[name="name"]').value = d.name || '';
      form.querySelector('[name="start_at"]').value = d.start_at ? d.start_at.slice(0, 16) : '';
      form.querySelector('[name="end_at"]').value = d.end_at ? d.end_at.slice(0, 16) : '';
      form.querySelector('[name="invite_code"]').value = d.invite_code || '';
      form.querySelector('[name="points_multiplier"]').value = d.points_multiplier ?? 1;
      form.querySelector('[name="exchange_review"]').value = d.exchange_review ?? 1;
      form.querySelector('[name="status"]').value = d.status ?? 1;
      form.querySelector('[name="detail_content"]').value = d.detail_content || '';
      form.querySelector('[name="rules_content"]').value = d.rules_content || '';
      // 启动页图片
      const uploadEl = form.querySelector('[data-upload]');
      if (d.splash_image && uploadEl) {
        const preview = uploadEl.querySelector('.upload-preview');
        if (preview) preview.innerHTML = '<img src="' + d.splash_image + '" class="max-h-24 rounded">';
        // 添加 hidden input
        let hiddenInput = form.querySelector('input[name="splash_image"]');
        if (!hiddenInput) {
          hiddenInput = document.createElement('input');
          hiddenInput.type = 'hidden';
          hiddenInput.name = 'splash_image';
          uploadEl.appendChild(hiddenInput);
        }
        hiddenInput.value = d.splash_image;
      }
    }
  } catch (e) {
    // 无活动，显示空表单即可
  }

  // 数据加载完成后再初始化 CKEditor
  await initEditors();

  form.onsubmit = async (e) => {
    e.preventDefault();
    msgEl.classList.add('hidden');
    CkEditor.syncAll(); // 提交前同步富文本内容到 textarea
    const fd = new FormData(form);
    const body = Object.fromEntries(fd);
    body.exchange_review = parseInt(body.exchange_review, 10) || 0;
    body.status = parseInt(body.status, 10) ?? 1;
    body.points_multiplier = parseFloat(body.points_multiplier) || 1;
    if (!body.start_at) delete body.start_at;
    if (!body.end_at) delete body.end_at;
    if (!body.invite_code) delete body.invite_code;
    if (!body.splash_image) delete body.splash_image;

    try {
      const res = await api('/campaign', { method: 'POST', body: JSON.stringify(body) });
      msgEl.textContent = res.message || '保存成功';
      msgEl.className = 'mt-4 text-sm text-emerald-400';
      msgEl.classList.remove('hidden');
      setTimeout(() => msgEl.classList.add('hidden'), 3000);
    } catch (e) {
      msgEl.textContent = e.message || '保存失败';
      msgEl.className = 'mt-4 text-sm text-red-400';
      msgEl.classList.remove('hidden');
    }
  };
});
</script>
JS;
include __DIR__ . '/../layouts/app.php';
