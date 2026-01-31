/**
 * 图片/文件上传组件
 * 用法: <div data-upload="image" data-field="avatar"></div>
 * 或: Upload.init(containerElement) 初始化容器内动态添加的 upload 区域
 */
window.Upload = {
  init(container = document) {
    container.querySelectorAll?.('[data-upload]:not([data-upload-inited])')?.forEach(el => this.setup(el));
  },
  setup(el) {
    if (el.dataset.uploadInited) return;
    el.dataset.uploadInited = '1';
    const type = el.dataset.upload || 'image';
    const field = el.dataset.field || 'file';
    const accept = type === 'image' ? 'image/*' : '*/*';

    const input = document.createElement('input');
    input.type = 'file';
    input.accept = accept;
    input.className = 'hidden';
    input.addEventListener('change', async (e) => {
      const file = e.target.files?.[0];
      if (!file) return;
      const formData = new FormData();
      formData.append('file', file);
      try {
        const res = await apiFormData(`/upload/${type}`, formData);
        const url = res.data?.url || res.data?.path;
        el.dispatchEvent(new CustomEvent('uploaded', { detail: { url, data: res.data } }));
        const preview = el.querySelector('.upload-preview');
        if (preview && type === 'image') preview.innerHTML = `<img src="${url}" class="max-h-24 rounded">`;
        else if (el.querySelector('input[type="hidden"]')) el.querySelector('input[type="hidden"]').value = url;
      } catch (err) {
        Modal.open({ title: '上传失败', body: err.message });
      }
      input.value = '';
    });

    el.classList.add('cursor-pointer', 'border-2', 'border-dashed', 'border-slate-600', 'rounded-lg', 'p-4', 'text-center', 'hover:border-emerald-500/50', 'transition-colors');
    el.innerHTML = `
      <div class="upload-preview text-slate-500 text-sm">点击上传${type === 'image' ? '图片' : '文件'}</div>
      <input type="hidden" name="${field}">
    `;
    el.prepend(input);
    el.addEventListener('click', (e) => { if (!e.target.closest('img')) input.click(); });
  }
};
document.addEventListener('DOMContentLoaded', () => Upload.init());
