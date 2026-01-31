/**
 * CKEditor 富文本封装
 * 本地资源 + 高度设置 + 图片/文件上传
 * 用法：await CkEditor.init('textarea.ckeditor-target');
 *       CkEditor.syncAll();  // 表单提交前同步
 */
window.CkEditor = {
  _loadPromise: null,
  _editors: [],
  _basePath: '/build/libs/ckeditor',

  async _ensureLoaded() {
    if (typeof ClassicEditor !== 'undefined') return;
    if (this._loadPromise) return this._loadPromise;
    this._loadPromise = new Promise((resolve, reject) => {
      const s = document.createElement('script');
      s.src = this._basePath + '/ckeditor.js';
      s.onload = resolve;
      s.onerror = () => reject(new Error('CKEditor 加载失败'));
      document.head.appendChild(s);
    });
    return this._loadPromise;
  },

  _createUploadAdapter(loader) {
    const API_BASE = window.APP_BASE || '/admin';
    const TOKEN_KEY = window.APP_TOKEN_KEY || 'token';
    const uploadUrl = API_BASE + (window.APP_API_SUFFIX !== undefined ? (window.APP_API_SUFFIX || '') : '/api') + '/upload/image';

    return {
      upload() {
        return loader.file.then(file => {
          const formData = new FormData();
          formData.append('file', file);
          const token = localStorage.getItem(TOKEN_KEY);
          const headers = token ? { 'Authorization': 'Bearer ' + token } : {};
          return fetch(uploadUrl, {
            method: 'POST',
            headers,
            body: formData,
          }).then(res => res.json()).then(data => {
            if (data.code !== 0 && data.code !== undefined) throw new Error(data.message || '上传失败');
            const url = data.data?.url || data.url;
            if (!url) throw new Error('未返回文件地址');
            return { default: url.startsWith('http') ? url : (window.location.origin + (url.startsWith('/') ? '' : '/') + url) };
          });
        });
      },
      abort() {}
    };
  },

  async init(selector, options = {}) {
    await this._ensureLoaded();
    const elms = document.querySelectorAll(selector);
    const height = options.height ?? 320;
    const enableUpload = options.uploadAdapter !== false;

    for (const el of elms) {
      if (el.tagName !== 'TEXTAREA' || el.dataset.ckeditorInited) continue;
      el.dataset.ckeditorInited = '1';

      const config = {
        placeholder: options.placeholder || el.placeholder || '',
        language: options.language || 'zh-cn',
        ...options,
      };
      delete config.height;
      delete config.uploadAdapter;

      const editor = await ClassicEditor.create(el, config);

      if (enableUpload && editor.plugins.has('FileRepository')) {
        editor.plugins.get('FileRepository').createUploadAdapter = loader => this._createUploadAdapter(loader);
      }

      const editable = el.parentElement?.querySelector('.ck-editor__editable');
      if (editable) {
        editable.style.minHeight = (typeof height === 'number' ? height + 'px' : String(height));
      }

      this._editors.push({ editor, textarea: el });
    }
    return this._editors;
  },

  syncAll() {
    this._editors.forEach(({ editor }) => {
      if (editor && typeof editor.updateSourceElement === 'function') editor.updateSourceElement();
    });
  },

  destroyAll() {
    this._editors.forEach(({ editor, textarea }) => {
      if (editor && typeof editor.destroy === 'function') {
        editor.destroy();
        delete textarea.dataset.ckeditorInited;
      }
    });
    this._editors = [];
  },
};
