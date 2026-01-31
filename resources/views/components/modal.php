<!-- 统一弹窗组件 - 通过 Modal API 调用 -->
<template id="modal-tpl">
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-show="open" x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0">
    <div class="absolute inset-0 bg-black/60" @click="close()"></div>
    <div class="relative w-full max-w-lg rounded-xl bg-slate-800 border border-slate-600/50 shadow-2xl"
      x-show="open"
      x-transition:enter="transition ease-out duration-200"
      x-transition:enter-start="opacity-0 scale-95"
      x-transition:enter-end="opacity-100 scale-100"
      @click.stop>
      <div class="p-5 border-b border-slate-700" x-show="title">
        <h3 class="text-lg font-medium text-slate-100" x-text="title"></h3>
      </div>
      <div class="p-5 text-slate-300" x-html="body"></div>
      <div class="p-5 border-t border-slate-700 flex justify-end gap-2">
        <button type="button" class="btn-secondary" @click="close()">取消</button>
        <button type="button" class="btn-primary" @click="onConfirm()" x-show="confirmText">确定</button>
      </div>
    </div>
  </div>
</template>
