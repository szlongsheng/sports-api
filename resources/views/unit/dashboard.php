<?php
$title = '单位端 - 仪表盘';
$showSidebar = true;
$showHeader = true;
$pageTitle = '仪表盘';
$baseUrl = '/unit';
$siteName = '单位端';
$navItems = unit_nav_items('/unit/dashboard');
$content = <<<'HTML'
<!-- 欢迎卡片 -->
<div class="mb-8">
  <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500/10 via-emerald-600/5 to-blue-500/10 border border-emerald-500/20 p-8">
    <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/5 rounded-full blur-3xl"></div>
    <div class="relative z-10">
      <div class="flex items-start justify-between">
        <div>
          <h2 class="text-2xl font-bold mb-2 transition-colors dark:text-white light:text-slate-900">欢迎回来！</h2>
          <p class="text-base transition-colors dark:text-slate-400 light:text-slate-600">让我们开始今天的工作</p>
        </div>
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-xl shadow-emerald-500/20">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
          </svg>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 功能卡片 -->
<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
  <div class="group relative overflow-hidden rounded-2xl backdrop-blur-sm border p-6 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 dark:bg-slate-800/50 dark:border-slate-700/50 dark:hover:border-emerald-500/30 dark:hover:shadow-emerald-500/10 light:bg-white light:border-slate-200 light:hover:border-emerald-500/30 light:hover:shadow-emerald-500/10">
    <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-all"></div>
    <div class="relative z-10">
      <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
        <svg class="w-6 h-6 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
        </svg>
      </div>
      <h3 class="text-lg font-semibold mb-2 transition-colors dark:text-white light:text-slate-900">单位信息</h3>
      <p class="text-sm mb-4 transition-colors dark:text-slate-400 light:text-slate-600">管理和更新单位基本信息</p>
      <a href="/unit/profile" class="text-emerald-600 dark:text-emerald-400 text-sm font-medium flex items-center gap-1 group-hover:gap-2 transition-all">
        查看详情
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
      </a>
    </div>
  </div>

  <div class="group relative overflow-hidden rounded-2xl backdrop-blur-sm border p-6 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 dark:bg-slate-800/50 dark:border-slate-700/50 dark:hover:border-blue-500/30 dark:hover:shadow-blue-500/10 light:bg-white light:border-slate-200 light:hover:border-blue-500/30 light:hover:shadow-blue-500/10">
    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-all"></div>
    <div class="relative z-10">
      <div class="w-12 h-12 rounded-xl bg-blue-500/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
        <svg class="w-6 h-6 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
      </div>
      <h3 class="text-lg font-semibold mb-2 transition-colors dark:text-white light:text-slate-900">人员管理</h3>
      <p class="text-sm mb-4 transition-colors dark:text-slate-400 light:text-slate-600">添加、编辑和管理单位人员</p>
      <a href="#" class="text-blue-600 dark:text-blue-400 text-sm font-medium flex items-center gap-1 group-hover:gap-2 transition-all">
        即将上线
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
      </a>
    </div>
  </div>

  <div class="group relative overflow-hidden rounded-2xl backdrop-blur-sm border p-6 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 dark:bg-slate-800/50 dark:border-slate-700/50 dark:hover:border-purple-500/30 dark:hover:shadow-purple-500/10 light:bg-white light:border-slate-200 light:hover:border-purple-500/30 light:hover:shadow-purple-500/10">
    <div class="absolute top-0 right-0 w-32 h-32 bg-purple-500/5 rounded-full blur-2xl group-hover:bg-purple-500/10 transition-all"></div>
    <div class="relative z-10">
      <div class="w-12 h-12 rounded-xl bg-purple-500/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
        <svg class="w-6 h-6 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
      </div>
      <h3 class="text-lg font-semibold mb-2 transition-colors dark:text-white light:text-slate-900">数据统计</h3>
      <p class="text-sm mb-4 transition-colors dark:text-slate-400 light:text-slate-600">查看详细的数据分析报告</p>
      <a href="#" class="text-purple-600 dark:text-purple-400 text-sm font-medium flex items-center gap-1 group-hover:gap-2 transition-all">
        即将上线
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
      </a>
    </div>
  </div>
</div>
HTML;
$scripts = <<<'JS'
<script>
document.addEventListener('DOMContentLoaded', () => {
  window.APP_BASE = '/unit';
  window.APP_API_SUFFIX = '/api';
  if (!localStorage.getItem('unit_token')) location.href = '/unit/login';
});
</script>
JS;
include __DIR__ . '/../layouts/app.php';
