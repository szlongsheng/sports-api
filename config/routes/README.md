# 路由配置说明

## 📁 文件结构

```
config/
├── routes.php          # 主路由文件（入口）
└── routes/
    ├── admin.php       # 管理端路由
    ├── unit.php        # 单位端路由
    ├── api.php         # 小程序API路由
    └── README.md       # 本说明文档
```

## 📋 各文件职责

### 1. `routes.php` - 主路由入口
- 定义全局路由（如 `/`, `/ping`）
- 加载各端的路由配置文件
- 保持简洁，只负责路由分发

### 2. `routes/admin.php` - 管理端路由
**路径前缀**: `/admin`

包含：
- 根路径智能跳转
- 页面路由（登录页、控制台、用户管理等）
- 认证接口（登录）
- API路由（CRUD、批量操作、文件上传等）

### 3. `routes/unit.php` - 单位端路由
**路径前缀**: `/unit`

包含：
- 根路径智能跳转
- 页面路由（登录页、控制台、单位信息等）
- 认证接口（登录）
- API路由（单位信息、文件上传等）

### 4. `routes/api.php` - 小程序API路由
**路径前缀**: `/api`

包含：
- 公开接口（微信登录、系统配置等）
- 需要认证的接口（用户信息等）

## 🎯 使用方法

### 添加新路由

#### 方法 1: 添加到对应的端
根据路由类型，直接在对应文件中添加：

**管理端路由** → `config/routes/admin.php`
```php
// 添加页面路由
$group->get('/settings', \App\Controllers\Admin\SettingsController::class . ':index');

// 添加API路由（在 /api group 内）
$inner->get('/settings', \App\Controllers\Admin\SettingsController::class . ':getSettings');
```

**单位端路由** → `config/routes/unit.php`
```php
// 添加页面路由
$group->get('/members', \App\Controllers\Unit\MemberController::class . ':index');

// 添加API路由（在 /api group 内）
$inner->get('/members', \App\Controllers\Unit\MemberController::class . ':list');
```

**小程序API** → `config/routes/api.php`
```php
// 公开接口（在主 group 内）
$group->get('/banners', \App\Controllers\Api\BannerController::class . ':index');

// 需要认证的接口（在认证 group 内）
$inner->get('/orders', \App\Controllers\Api\OrderController::class . ':index');
```

#### 方法 2: 创建新的路由文件
如果某个功能模块路由很多，可以创建独立文件：

```php
// 1. 创建 config/routes/shop.php
return function (App $app): void {
    $app->group('/shop', function (RouteCollectorProxy $group) {
        // ... 商城相关路由
    });
};

// 2. 在 config/routes.php 中引入
(require __DIR__ . '/routes/shop.php')($app);
```

## 🔐 中间件说明

### JWT 认证中间件
```php
->add(new JwtAuthMiddleware('admin'))  // 管理端认证
->add(new JwtAuthMiddleware('unit'))   // 单位端认证
->add(new JwtAuthMiddleware('api'))    // 小程序认证
```

三种认证互相独立，token 不通用。

## 📊 路由分组结构

### 管理端 (`admin.php`)
```
/admin
├── (根路径) → 智能跳转
├── /login → 登录页
├── /dashboard → 控制台
├── /users → 用户管理页
├── /auth/login → 登录API
└── /api/ (需要JWT)
    ├── /users → 用户CRUD
    ├── /users/batch → 批量操作
    └── /upload/* → 文件上传
```

### 单位端 (`unit.php`)
```
/unit
├── (根路径) → 智能跳转
├── /login → 登录页
├── /dashboard → 控制台
├── /profile → 单位信息页
├── /auth/login → 登录API
└── /api/ (需要JWT)
    ├── /profile → 单位信息
    └── /upload/* → 文件上传
```

### 小程序API (`api.php`)
```
/api
├── /auth/wxlogin → 微信登录（公开）
├── /config → 系统配置（公开）
└── (需要JWT)
    └── /user/info → 用户信息
```

## ✨ 优势

### 1. **清晰的职责分离**
- 每个文件只负责一端的路由
- 易于查找和维护

### 2. **更好的可扩展性**
- 添加新路由时不会影响其他端
- 路由多了也不会显得混乱

### 3. **团队协作友好**
- 不同人员可以同时修改不同的路由文件
- 减少 Git 冲突

### 4. **代码组织规范**
- 统一的注释和分组
- 易于理解和交接

## 🚀 最佳实践

1. **保持文件简洁**
   - 单个路由文件不要超过 200 行
   - 功能复杂时考虑进一步拆分

2. **统一命名规范**
   - 页面路由使用名词：`/users`, `/profile`
   - API路由使用 RESTful 风格

3. **添加清晰注释**
   - 每个路由组都要有注释说明
   - 复杂路由添加用途说明

4. **认证中间件使用**
   - 公开接口放在外层
   - 需要认证的放在 JWT 中间件内

## 📝 示例：添加新功能路由

假设要添加"通知管理"功能：

```php
// config/routes/admin.php

// 在页面路由部分添加
$group->get('/notifications', \App\Controllers\Common\PageController::class . ':adminNotifications');

// 在 API 路由部分添加
$inner->get('/notifications', \App\Controllers\Admin\NotificationController::class . ':index');
$inner->post('/notifications', \App\Controllers\Admin\NotificationController::class . ':create');
$inner->put('/notifications/{id}', \App\Controllers\Admin\NotificationController::class . ':update');
$inner->delete('/notifications/{id}', \App\Controllers\Admin\NotificationController::class . ':delete');
$inner->post('/notifications/batch', \App\Controllers\Admin\NotificationController::class . ':batchSend');
```

---

更新时间: 2026-01-31
版本: 1.0
