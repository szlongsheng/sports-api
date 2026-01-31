<?php $title = '登录 - 管理后台'; ?>
<!DOCTYPE html>
<html lang="zh-CN" x-data="themeState()" :class="isDark ? 'dark' : 'light'">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?></title>
  <link href="/build/css/app.css" rel="stylesheet">
  <style>
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
<body class="min-h-screen flex items-center justify-center p-4 transition-colors duration-300" 
      :class="isDark ? 'bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950' : 'bg-gradient-to-br from-slate-50 via-white to-slate-50'">
  <div class="w-full max-w-md relative z-10">
    <!-- 主题切换按钮 -->
    <div class="flex justify-end mb-4">
      <button @click="toggleTheme()" class="p-3 rounded-xl transition-all" 
              :class="isDark ? 'bg-slate-800/50 hover:bg-slate-800 text-slate-400 hover:text-white' : 'bg-white hover:bg-slate-50 text-slate-600 hover:text-slate-900 border border-slate-200'"
              title="切换主题">
        <svg x-show="isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
        <svg x-show="!isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
        </svg>
      </button>
    </div>
    
    <!-- Logo 和标题 -->
    <div class="text-center mb-8">
      <div class="inline-flex items-center justify-center w-16 h-16 mb-4 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 shadow-2xl shadow-emerald-500/30">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
        </svg>
      </div>
      <h1 class="text-3xl font-bold mb-2 transition-colors" :class="isDark ? 'text-white' : 'text-slate-900'">管理后台</h1>
      <p class="transition-colors" :class="isDark ? 'text-slate-400' : 'text-slate-600'">欢迎回来，请登录您的管理员账号</p>
    </div>

    <!-- 登录表单 -->
    <form id="login-form" class="relative overflow-hidden rounded-2xl backdrop-blur-xl border p-8 shadow-2xl transition-colors"
          :class="isDark ? 'bg-slate-900/60 border-slate-700/50' : 'bg-white/80 border-slate-200'">
      <div class="absolute top-0 right-0 w-40 h-40 bg-emerald-500/5 rounded-full blur-3xl"></div>
      
      <div class="relative z-10 space-y-5">
        <div>
          <label class="block text-sm font-medium mb-2 transition-colors" :class="isDark ? 'text-slate-300' : 'text-slate-700'">用户名</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="w-5 h-5 transition-colors" :class="isDark ? 'text-slate-500' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
            </div>
            <input type="text" name="username" placeholder="请输入用户名" required
              class="input !pl-10 w-full" 
              autocomplete="username">
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium mb-2 transition-colors" :class="isDark ? 'text-slate-300' : 'text-slate-700'">密码</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="w-5 h-5 transition-colors" :class="isDark ? 'text-slate-500' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
              </svg>
            </div>
            <input type="password" name="password" placeholder="请输入密码" required
              class="input !pl-10 w-full" 
              autocomplete="current-password">
          </div>
        </div>

        <div class="flex items-center justify-between text-sm">
          <label class="flex items-center cursor-pointer transition-colors" :class="isDark ? 'text-slate-400' : 'text-slate-600'">
            <input type="checkbox" class="rounded text-emerald-500 mr-2 transition-colors"
                   :class="isDark ? 'border-slate-600 bg-slate-800 focus:ring-emerald-500/20' : 'border-slate-300 bg-white focus:ring-emerald-500/20'">
            <span>记住我</span>
          </label>
          <a href="#" class="text-emerald-600 hover:text-emerald-500 transition-colors" :class="isDark ? 'text-emerald-400 hover:text-emerald-300' : 'text-emerald-600 hover:text-emerald-500'">忘记密码？</a>
        </div>

        <button type="submit" class="btn-primary w-full py-3 text-base font-semibold shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 hover:scale-[1.02] active:scale-100 transition-all">
          登录
        </button>
      </div>
    </form>

    <p id="login-error" class="mt-4 p-4 text-sm text-red-400 bg-red-500/10 border border-red-500/20 rounded-xl text-center hidden"></p>

    <!-- 底部信息 -->
    <div class="mt-6 text-center text-sm transition-colors" :class="isDark ? 'text-slate-500' : 'text-slate-400'">
      <p>© 2026 体育管理系统. All rights reserved.</p>
    </div>
  </div>
  <script src="/build/libs/alpine.min.js" defer></script>
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
  <script>
    // 检查是否已登录（使用管理端专用 token 键，与单位端分离）
    if (localStorage.getItem('admin_token')) {
      window.location.href = '/admin/dashboard';
    }
    
    document.getElementById('login-form').onsubmit = async (e) => {
      e.preventDefault();
      const fd = new FormData(e.target);
      const errEl = document.getElementById('login-error');
      try {
        const res = await fetch('/admin/auth/login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(Object.fromEntries(fd)),
        });
        const data = await res.json();
        if (data.code === 0 && data.data?.token) {
          localStorage.setItem('admin_token', data.data.token);
          localStorage.setItem('user', JSON.stringify(data.data.user || { username: fd.get('username') }));
          location.href = '/admin/dashboard';
        } else {
          errEl.textContent = data.message || '登录失败';
          errEl.classList.remove('hidden');
        }
      } catch (e) {
        errEl.textContent = '网络错误';
        errEl.classList.remove('hidden');
      }
    };
  </script>
</body>
</html>
