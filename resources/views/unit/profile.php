<?php
$title = '单位信息';
$showSidebar = true;
$showHeader = true;
$pageTitle = '单位信息';
$baseUrl = '/unit';
$siteName = '单位端';
$navItems = [
  ['label' => '仪表盘', 'url' => '/unit/dashboard', 'active' => false],
  ['label' => '单位信息', 'url' => '/unit/profile', 'active' => true],
];
$content = <<<'HTML'
<div class="mb-6">
  <h2 class="text-2xl font-bold mb-2 transition-colors dark:text-white light:text-slate-900">单位信息</h2>
  <p class="text-sm transition-colors dark:text-slate-400 light:text-slate-600">查看和管理单位基本信息</p>
</div>

<div class="grid gap-6 md:grid-cols-2">
  <div id="profile-container" class="md:col-span-2 rounded-2xl backdrop-blur-xl border shadow-2xl p-8 transition-colors dark:bg-slate-900/60 dark:border-slate-700/50 light:bg-white light:border-slate-200">
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
  window.APP_BASE = '/unit';
  window.APP_API_SUFFIX = '/api';
  if (!localStorage.getItem('unit_token')) { location.href = '/unit/login'; return; }
  try {
    const res = await api('/profile');
    const d = res.data?.profile || res.data || {};
    const isDark = document.documentElement.classList.contains('dark');
    document.getElementById('profile-container').innerHTML = `
      <div class="space-y-6">
        <!-- 头部信息 -->
        <div class="flex items-center gap-6 pb-6 border-b transition-colors ${isDark ? 'border-slate-700/50' : 'border-slate-200'}">
          <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-xl shadow-emerald-500/20">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
          </div>
          <div class="flex-1">
            <h3 class="text-2xl font-bold mb-1 transition-colors ${isDark ? 'text-white' : 'text-slate-900'}">${d.account || '单位账号'}</h3>
            <p class="transition-colors ${isDark ? 'text-slate-400' : 'text-slate-600'}">单位 ID: ${d.id || '-'}</p>
          </div>
          <button class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-medium shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 transition-all">
            编辑资料
          </button>
        </div>

        <!-- 详细信息 -->
        <div class="grid gap-6 md:grid-cols-2">
          <div class="space-y-4">
            <h4 class="text-sm font-semibold uppercase tracking-wider mb-4 transition-colors ${isDark ? 'text-slate-400' : 'text-slate-600'}">基本信息</h4>
            <div class="space-y-4">
              <div class="group p-4 rounded-xl border transition-all ${isDark ? 'bg-slate-800/30 hover:bg-slate-800/50 border-slate-700/30 hover:border-slate-600/50' : 'bg-slate-50 hover:bg-slate-100 border-slate-200 hover:border-slate-300'}">
                <dt class="text-sm mb-1 flex items-center gap-2 transition-colors ${isDark ? 'text-slate-500' : 'text-slate-500'}">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                  <span>账号名称</span>
                </dt>
                <dd class="font-medium transition-colors ${isDark ? 'text-white' : 'text-slate-900'}">${d.account || '-'}</dd>
              </div>
              
              <div class="group p-4 rounded-xl border transition-all ${isDark ? 'bg-slate-800/30 hover:bg-slate-800/50 border-slate-700/30 hover:border-slate-600/50' : 'bg-slate-50 hover:bg-slate-100 border-slate-200 hover:border-slate-300'}">
                <dt class="text-sm mb-1 flex items-center gap-2 transition-colors ${isDark ? 'text-slate-500' : 'text-slate-500'}">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                  </svg>
                  <span>单位ID</span>
                </dt>
                <dd class="font-medium font-mono transition-colors ${isDark ? 'text-white' : 'text-slate-900'}">#${d.id || '-'}</dd>
              </div>
            </div>
          </div>

          <div class="space-y-4">
            <h4 class="text-sm font-semibold uppercase tracking-wider mb-4 transition-colors ${isDark ? 'text-slate-400' : 'text-slate-600'}">账户状态</h4>
            <div class="space-y-4">
              <div class="group p-4 rounded-xl border transition-all ${isDark ? 'bg-slate-800/30 hover:bg-slate-800/50 border-slate-700/30 hover:border-slate-600/50' : 'bg-slate-50 hover:bg-slate-100 border-slate-200 hover:border-slate-300'}">
                <dt class="text-sm mb-1 flex items-center gap-2 transition-colors ${isDark ? 'text-slate-500' : 'text-slate-500'}">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span>账户状态</span>
                </dt>
                <dd>
                  <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/10 text-sm font-medium rounded-lg ${isDark ? 'text-emerald-400' : 'text-emerald-600'}">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                    <span>正常</span>
                  </span>
                </dd>
              </div>
              
              <div class="group p-4 rounded-xl border transition-all ${isDark ? 'bg-slate-800/30 hover:bg-slate-800/50 border-slate-700/30 hover:border-slate-600/50' : 'bg-slate-50 hover:bg-slate-100 border-slate-200 hover:border-slate-300'}">
                <dt class="text-sm mb-1 flex items-center gap-2 transition-colors ${isDark ? 'text-slate-500' : 'text-slate-500'}">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span>创建时间</span>
                </dt>
                <dd class="font-medium transition-colors ${isDark ? 'text-white' : 'text-slate-900'}">${d.created_at || '-'}</dd>
              </div>
            </div>
          </div>
        </div>

        <!-- 操作提示 -->
        <div class="mt-6 p-4 rounded-xl bg-blue-500/10 border border-blue-500/20">
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5 ${isDark ? 'text-blue-400' : 'text-blue-500'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
              <p class="text-sm font-medium mb-1 ${isDark ? 'text-blue-400' : 'text-blue-600'}">提示</p>
              <p class="text-sm transition-colors ${isDark ? 'text-slate-300' : 'text-slate-600'}">如需修改单位信息，请点击右上角"编辑资料"按钮，或联系管理员协助处理。</p>
            </div>
          </div>
        </div>
      </div>
    `;
  } catch (e) {
    const isDark = document.documentElement.classList.contains('dark');
    document.getElementById('profile-container').innerHTML = `
      <div class="text-center py-12">
        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-red-500/10 flex items-center justify-center">
          <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <p class="text-red-400 text-lg font-medium mb-2">加载失败</p>
        <p class="text-sm transition-colors ${isDark ? 'text-slate-500' : 'text-slate-400'}">无法获取单位信息，请稍后重试</p>
      </div>
    `;
  }
});
</script>
JS;
include __DIR__ . '/../layouts/app.php';
