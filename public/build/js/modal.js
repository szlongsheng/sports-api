/**
 * 统一弹窗组件
 */
window.Modal = {
  open(options = {}) {
    const { title = '', body = '', confirmText = '确定', onConfirm } = options;
    const isDark = document.documentElement.classList.contains('dark');
    let root = document.getElementById('modal-root');
    if (!root) {
      root = document.createElement('div');
      root.id = 'modal-root';
      document.body.appendChild(root);
    }
    root.innerHTML = `
      <div id="modal-instance" class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-sm" style="background: ${isDark ? 'rgba(0,0,0,0.7)' : 'rgba(0,0,0,0.5)'}; animation: fadeIn 0.2s ease-out;">
        <div class="relative w-full max-w-2xl max-h-[90vh] flex flex-col rounded-2xl backdrop-blur-xl border shadow-2xl ${isDark ? 'bg-slate-900/95 border-slate-700/50' : 'bg-white/95 border-slate-200'}" style="animation: scaleIn 0.2s ease-out;">
          <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/5 rounded-full blur-3xl"></div>
          <div class="relative z-10 flex flex-col min-h-0 flex-1">
            <div class="flex-shrink-0 flex items-center justify-between p-6 border-b ${isDark ? 'border-slate-700/50' : 'border-slate-200'}">
              <h3 class="text-xl font-bold ${isDark ? 'text-white' : 'text-slate-900'}">${title}</h3>
              <button type="button" class="modal-cancel transition-colors ${isDark ? 'text-slate-400 hover:text-white' : 'text-slate-500 hover:text-slate-900'}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
            <div class="flex-1 min-h-0 overflow-y-auto p-6 ${isDark ? 'text-slate-300' : 'text-slate-700'}">${body}</div>
            <div class="flex-shrink-0 p-6 border-t ${isDark ? 'border-slate-700/50' : 'border-slate-200'} flex justify-end gap-3">
              <button type="button" class="btn-secondary modal-cancel-btn px-5 py-2.5">取消</button>
              ${confirmText ? `<button type="button" class="btn-primary modal-confirm px-5 py-2.5 shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40">${confirmText}</button>` : ''}
            </div>
          </div>
        </div>
      </div>
    `;
    const close = () => {
      const instance = root.querySelector('#modal-instance');
      if (instance) {
        instance.style.animation = 'fadeOut 0.15s ease-in';
        setTimeout(() => { root.innerHTML = ''; }, 150);
      } else {
        root.innerHTML = '';
      }
    };
    root.querySelector('.modal-cancel')?.addEventListener('click', close);
    root.querySelector('.modal-cancel-btn')?.addEventListener('click', close);
    root.querySelector('.modal-confirm')?.addEventListener('click', () => {
      if (onConfirm) onConfirm();
      close();
    });
    root.querySelector('#modal-instance')?.addEventListener('click', (e) => {
      if (e.target.id === 'modal-instance') close();
    });
    return { close };
  },
  confirm(message, onConfirm) {
    const isDark = document.documentElement.classList.contains('dark');
    return this.open({ title: '确认操作', body: `<p class="${isDark ? 'text-slate-300' : 'text-slate-700'}">${message}</p>`, confirmText: '确定', onConfirm });
  }
};
