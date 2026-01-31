<!DOCTYPE html>
<html lang="zh-CN" x-data="themeState()" :class="isDark ? 'dark' : 'light'">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? '管理后台') ?></title>
  <link href="/build/css/app.css" rel="stylesheet">
  <style>
    /* 使用系统字体作为后备，避免依赖外部 CDN */
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Noto Sans", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji";
    }
    
    /* 暗色模式背景 */
    .dark body::before {
      content: '';
      position: fixed;
      inset: 0;
      background: 
        radial-gradient(circle at 20% 10%, rgba(16, 185, 129, 0.15) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(59, 130, 246, 0.1) 0%, transparent 50%);
      pointer-events: none;
      z-index: 0;
    }
    
    /* 亮色模式背景 */
    .light body::before {
      content: '';
      position: fixed;
      inset: 0;
      background: 
        radial-gradient(circle at 20% 10%, rgba(16, 185, 129, 0.08) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(59, 130, 246, 0.06) 0%, transparent 50%);
      pointer-events: none;
      z-index: 0;
    }
  </style>
</head>
<body class="min-h-screen transition-colors duration-300" 
      :class="isDark ? 'bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950' : 'bg-gradient-to-br from-slate-50 via-white to-slate-50'">
  <div class="min-h-screen relative" x-data="appState()">
    <?php if (!empty($showSidebar)): ?>
    <aside class="fixed top-0 left-0 w-64 h-screen backdrop-blur-xl border-r flex flex-col shadow-2xl z-20 transition-colors duration-300"
           :class="isDark ? 'bg-slate-900/95 border-slate-700/30' : 'bg-white/95 border-slate-200/50'">
      <div class="p-6 border-b transition-colors duration-300" :class="isDark ? 'border-slate-700/30' : 'border-slate-200/50'">
        <a href="<?= $baseUrl ?? '/admin' ?>" class="flex items-center gap-3 group">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/20 group-hover:shadow-emerald-500/40 transition-shadow">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
          </div>
          <span class="text-lg font-bold bg-gradient-to-r from-emerald-400 to-emerald-300 bg-clip-text text-transparent"><?= htmlspecialchars($siteName ?? '管理后台') ?></span>
        </a>
      </div>
      <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
        <?php foreach ($navItems ?? [] as $item): ?>
        <a href="<?= htmlspecialchars($item['url']) ?>" class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 <?= (!empty($item['active'])) ? 'bg-gradient-to-r from-emerald-500/20 to-emerald-600/10 text-emerald-400 border border-emerald-500/20 shadow-lg shadow-emerald-500/10' : '' ?>"
           :class="'<?= (!empty($item['active'])) ? '' : 'hover:bg-slate-800/60' ?>' + (isDark ? ' text-slate-400 hover:text-white' : ' text-slate-600 hover:text-slate-900 hover:bg-slate-100')"
          <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
          <span class="font-medium"><?= htmlspecialchars($item['label']) ?></span>
        </a>
        <?php endforeach; ?>
      </nav>
      <div class="p-4 border-t transition-colors duration-300" 
           :class="isDark ? 'border-slate-700/30 bg-slate-800/30' : 'border-slate-200/50 bg-slate-50/50'">
        <div class="flex items-center gap-3 px-3 py-2">
          <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors"
               :class="isDark ? 'bg-gradient-to-br from-slate-700 to-slate-600' : 'bg-gradient-to-br from-slate-200 to-slate-300'">
            <svg class="w-4 h-4 transition-colors" :class="isDark ? 'text-slate-300' : 'text-slate-600'" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium truncate transition-colors" :class="isDark ? 'text-slate-300' : 'text-slate-700'" x-text="user?.name || user?.username || user?.account || '用户'"></p>
            <p class="text-xs transition-colors" :class="isDark ? 'text-slate-500' : 'text-slate-400'">在线</p>
          </div>
        </div>
      </div>
    </aside>
    <?php endif; ?>
    <main class="<?= !empty($showSidebar) ? 'ml-64' : '' ?> flex-1 flex flex-col min-w-0 relative z-10 min-h-screen">
      <?php if (!empty($showHeader)): ?>
      <header class="h-16 flex-shrink-0 flex items-center justify-between px-8 border-b backdrop-blur-xl transition-colors duration-300"
              :class="isDark ? 'border-slate-700/30 bg-slate-900/60' : 'border-slate-200/50 bg-white/60'">
        <div>
          <h1 class="text-xl font-bold transition-colors" :class="isDark ? 'text-white' : 'text-slate-900'"><?= htmlspecialchars($pageTitle ?? '') ?></h1>
          <p class="text-xs mt-0.5 transition-colors" :class="isDark ? 'text-slate-500' : 'text-slate-400'">欢迎回来，让我们开始工作吧</p>
        </div>
        <div class="flex items-center gap-4">
          <!-- 主题切换按钮 -->
          <button @click="toggleTheme()" class="relative p-2 rounded-lg transition-colors" 
                  :class="isDark ? 'text-slate-400 hover:text-white hover:bg-slate-800/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'" 
                  title="切换主题">
            <svg x-show="isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <svg x-show="!isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
            </svg>
          </button>
          
          <button class="relative p-2 rounded-lg transition-colors"
                  :class="isDark ? 'text-slate-400 hover:text-white hover:bg-slate-800/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-emerald-500 rounded-full ring-2" 
                  :class="isDark ? 'ring-slate-900' : 'ring-white'"></span>
          </button>
          
          <div class="w-px h-6 transition-colors" :class="isDark ? 'bg-slate-700/50' : 'bg-slate-200'"></div>
          
          <button @click="logout()" class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg transition-colors"
                  :class="isDark ? 'text-slate-400 hover:text-white hover:bg-slate-800/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            <span>退出</span>
          </button>
        </div>
      </header>
      <?php endif; ?>
      <div class="flex-1 p-8 overflow-auto">
        <?= $content ?? '' ?>
      </div>
    </main>
  </div>
  <div id="modal-root"></div>
  <script src="/build/libs/alpine.min.js" defer></script>
  <script>
    // 各端独立 token 键，避免管理端与单位端共用导致误跳转
    window.APP_BASE = '<?= htmlspecialchars($baseUrl ?? '/admin') ?>';
    window.APP_TOKEN_KEY = (window.APP_BASE === '/unit' ? 'unit_token' : 'admin_token');
  </script>
  <script>
    // 主题状态管理
    function themeState() {
      return {
        isDark: localStorage.getItem('theme') !== 'light',
        toggleTheme() {
          this.isDark = !this.isDark;
          localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
        }
      };
    }
  </script>
  <script src="/build/js/api.js"></script>
  <script src="/build/js/state.js"></script>
  <script src="/build/js/modal.js"></script>
  <script src="/build/js/ckeditor.js"></script>
  <script src="/build/js/crud.js"></script>
  <script src="/build/js/upload.js"></script>
  <?= $scripts ?? '' ?>
</body>
</html>
