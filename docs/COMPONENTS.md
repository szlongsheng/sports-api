# 管理端/单位端 共享组件文档

## 一、后端组件

### 1. 分页 PageService
```php
$pageService = new \App\Services\PageService();
$result = $pageService->paginate($query, $page, $perPage);
// 返回: ['list', 'total', 'page', 'per_page', 'total_pages']
```

### 2. 筛选 HasFilters Trait
在 Controller 中 use，定义 `$filterMap`：
```php
protected array $filterMap = [
    'keyword' => ['field' => 'username', 'op' => 'like'],
    'status'  => 'status',
];
$this->applyFilters($query, $params);
```

### 3. CRUD 基类 BaseCrudController
继承后实现 `createModel()`，配置 `$modelClass`、`$filterMap` 等。

### 4. 批量操作 BatchController
继承后配置 `$modelClass`，支持 action: delete、enable、disable。

### 5. OSS/本地 文件上传
- **POST** `/admin/api/upload/image` 或 `/unit/api/upload/image`
- Body: multipart/form-data, 字段 `file`
- 配置 OSS 见 .env.example，不配置则存本地 `public/uploads/`

---

## 二、前端组件

### 1. 统一弹窗 Modal
```js
Modal.open({ title: '标题', body: '<p>内容</p>', confirmText: '确定', onConfirm: () => {} });
Modal.confirm('确定删除？', () => { /* 确认后执行 */ });
```

### 2. 分页列表 CrudPage
```js
CrudPage({
  container: 'crud-container',
  apiPath: '/users',
  columns: [
    { key: 'id', label: 'ID' },
    { key: 'name', label: '姓名' },
    { key: 'status', label: '状态', render: (v) => v === 1 ? '启用' : '禁用' },
  ],
  filters: [{ key: 'keyword', placeholder: '搜索' }],
  batchActions: ['delete', 'enable', 'disable'],
  rowActions: (row) => `<button class="row-delete" data-id="${row.id}">删除</button>`,
  bindRowActions: (container, reload) => {
    container.addEventListener('click', (e) => {
      if (e.target.classList.contains('row-delete')) { /* ... */ }
    });
  },
});
```

### 3. 上传组件
```html
<div data-upload="image" data-field="avatar"></div>
```
或动态添加后调用 `Upload.init(modalElement)`。

---

## 三、路由约定
- 管理端页面: `/admin/login`、`/admin/dashboard`、`/admin/users`
- 管理端 API: `/admin/api/*`（需 JWT）
- 单位端页面: `/unit/login`、`/unit/dashboard`、`/unit/profile`
- 单位端 API: `/unit/api/*`（需 JWT）

## 四、样式
Tailwind + 深色主题，主色 emerald。运行 `npm run build` 构建 CSS。
