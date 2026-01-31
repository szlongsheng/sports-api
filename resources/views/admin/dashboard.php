<?php
$title = '仪表盘';
$showSidebar = true;
$showHeader = true;
$pageTitle = '仪表盘';
$baseUrl = '/admin';
$siteName = '管理后台';
$navItems = [
  ['label' => '仪表盘', 'url' => '/admin/dashboard', 'active' => true],
  ['label' => '管理员', 'url' => '/admin/users', 'active' => false],
  ['label' => '用户管理', 'url' => '/admin/members', 'active' => false],
  ['label' => '单位管理', 'url' => '/admin/units', 'active' => false],
];
$content = <<<'HTML'
<!-- 欢迎区域 -->
<div class="mb-8">
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-3xl font-bold mb-2 transition-colors dark:text-white light:text-slate-900">数据概览</h2>
      <p class="transition-colors dark:text-slate-400 light:text-slate-600">实时统计信息一览</p>
    </div>
    <button class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-medium shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 transition-all">
      刷新数据
    </button>
  </div>
</div>

<!-- 统计卡片 -->
<div class="grid gap-6 md:grid-cols-3 mb-8">
  <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500/10 to-emerald-600/5 border border-emerald-500/20 p-6 hover:border-emerald-500/40 transition-all duration-300 hover:shadow-xl hover:shadow-emerald-500/20 hover:-translate-y-1">
    <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all"></div>
    <div class="relative z-10">
      <div class="flex items-center justify-between mb-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-500/20 flex items-center justify-center">
          <svg class="w-6 h-6 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
          </svg>
        </div>
        <span class="px-3 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-medium rounded-lg">+12%</span>
      </div>
      <p class="text-sm mb-2 transition-colors dark:text-slate-400 light:text-slate-600">管理员总数</p>
      <p class="text-3xl font-bold transition-colors dark:text-white light:text-slate-900" id="stat-users">-</p>
      <p class="text-xs mt-2 transition-colors dark:text-slate-500 light:text-slate-500">较上月增长</p>
    </div>
  </div>

  <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500/10 to-blue-600/5 border border-blue-500/20 p-6 hover:border-blue-500/40 transition-all duration-300 hover:shadow-xl hover:shadow-blue-500/20 hover:-translate-y-1">
    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-all"></div>
    <div class="relative z-10">
      <div class="flex items-center justify-between mb-4">
        <div class="w-12 h-12 rounded-xl bg-blue-500/20 flex items-center justify-center">
          <svg class="w-6 h-6 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
          </svg>
        </div>
        <span class="px-3 py-1 bg-blue-500/10 text-blue-600 dark:text-blue-400 text-xs font-medium rounded-lg">+8%</span>
      </div>
      <p class="text-sm mb-2 transition-colors dark:text-slate-400 light:text-slate-600">单位数量</p>
      <p class="text-3xl font-bold transition-colors dark:text-white light:text-slate-900" id="stat-units">-</p>
      <p class="text-xs mt-2 transition-colors dark:text-slate-500 light:text-slate-500">较上月增长</p>
    </div>
  </div>

  <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-500/10 to-purple-600/5 border border-purple-500/20 p-6 hover:border-purple-500/40 transition-all duration-300 hover:shadow-xl hover:shadow-purple-500/20 hover:-translate-y-1">
    <div class="absolute top-0 right-0 w-32 h-32 bg-purple-500/10 rounded-full blur-2xl group-hover:bg-purple-500/20 transition-all"></div>
    <div class="relative z-10">
      <div class="flex items-center justify-between mb-4">
        <div class="w-12 h-12 rounded-xl bg-purple-500/20 flex items-center justify-center">
          <svg class="w-6 h-6 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
        </div>
        <span class="px-3 py-1 bg-purple-500/10 text-purple-600 dark:text-purple-400 text-xs font-medium rounded-lg">+15%</span>
      </div>
      <p class="text-sm mb-2 transition-colors dark:text-slate-400 light:text-slate-600">用户总数</p>
      <p class="text-3xl font-bold transition-colors dark:text-white light:text-slate-900" id="stat-all-users">-</p>
      <p class="text-xs mt-2 transition-colors dark:text-slate-500 light:text-slate-500">较上月增长</p>
    </div>
  </div>
</div>

<!-- 快捷操作 -->
<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
  <a href="/admin/users" class="group relative overflow-hidden rounded-2xl backdrop-blur-sm border p-6 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 dark:bg-slate-800/50 dark:border-slate-700/50 dark:hover:border-emerald-500/30 dark:hover:shadow-emerald-500/10 light:bg-white light:border-slate-200 light:hover:border-emerald-500/30 light:hover:shadow-emerald-500/10">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center group-hover:scale-110 transition-transform">
        <svg class="w-6 h-6 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
        </svg>
      </div>
      <div>
        <p class="font-semibold transition-colors dark:text-white light:text-slate-900">添加管理员</p>
        <p class="text-sm transition-colors dark:text-slate-500 light:text-slate-500">快速创建</p>
      </div>
    </div>
  </a>

  <button class="group relative overflow-hidden rounded-2xl backdrop-blur-sm border p-6 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 text-left dark:bg-slate-800/50 dark:border-slate-700/50 dark:hover:border-blue-500/30 dark:hover:shadow-blue-500/10 light:bg-white light:border-slate-200 light:hover:border-blue-500/30 light:hover:shadow-blue-500/10">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-blue-500/10 flex items-center justify-center group-hover:scale-110 transition-transform">
        <svg class="w-6 h-6 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
        </svg>
      </div>
      <div>
        <p class="font-semibold transition-colors dark:text-white light:text-slate-900">系统设置</p>
        <p class="text-sm transition-colors dark:text-slate-500 light:text-slate-500">配置管理</p>
      </div>
    </div>
  </button>

  <button class="group relative overflow-hidden rounded-2xl backdrop-blur-sm border p-6 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 text-left dark:bg-slate-800/50 dark:border-slate-700/50 dark:hover:border-purple-500/30 dark:hover:shadow-purple-500/10 light:bg-white light:border-slate-200 light:hover:border-purple-500/30 light:hover:shadow-purple-500/10">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-purple-500/10 flex items-center justify-center group-hover:scale-110 transition-transform">
        <svg class="w-6 h-6 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
      </div>
      <div>
        <p class="font-semibold transition-colors dark:text-white light:text-slate-900">数据报表</p>
        <p class="text-sm transition-colors dark:text-slate-500 light:text-slate-500">导出查看</p>
      </div>
    </div>
  </button>

  <button class="group relative overflow-hidden rounded-2xl backdrop-blur-sm border p-6 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 text-left dark:bg-slate-800/50 dark:border-slate-700/50 dark:hover:border-amber-500/30 dark:hover:shadow-amber-500/10 light:bg-white light:border-slate-200 light:hover:border-amber-500/30 light:hover:shadow-amber-500/10">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-amber-500/10 flex items-center justify-center group-hover:scale-110 transition-transform">
        <svg class="w-6 h-6 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
      </div>
      <div>
        <p class="font-semibold transition-colors dark:text-white light:text-slate-900">系统日志</p>
        <p class="text-sm transition-colors dark:text-slate-500 light:text-slate-500">查看记录</p>
      </div>
    </div>
  </button>
</div>
HTML;
$scripts = <<<'JS'
<script>
document.addEventListener('DOMContentLoaded', async () => {
  window.APP_BASE = '/admin';
  window.APP_API_SUFFIX = '/api';
  const token = localStorage.getItem('admin_token');
  if (!token) { location.href = '/admin/login'; return; }
  try {
    const u = await fetch('/admin/api/users?per_page=1', { headers: { 'Authorization': 'Bearer ' + token } }).then(r => r.json());
    document.getElementById('stat-users').textContent = u?.data?.total ?? '-';
  } catch (e) {}
});
</script>
JS;
include __DIR__ . '/../layouts/app.php';
