# 体育 API 开发文档

## 项目概述

基于 **PHP 7.4 + MySQL** 的快速开发框架，支持三端：

| 端 | 路径前缀 | 说明 |
|----|----------|------|
| Web 管理端 | `/admin` | 后台管理系统 |
| Web 单位端 | `/unit` | 单位/机构使用 |
| 小程序 API | `/api` | 微信小程序接口 |

## 技术栈

- **框架**: Slim 4（轻量 PSR-7 微框架）
- **数据库**: Illuminate Database (Eloquent ORM)
- **鉴权**: Firebase PHP-JWT
- **验证**: Respect/Validation
- **配置**: vlucas/phpdotenv
- **日志**: Monolog
- **OSS**: 阿里云 OSS SDK（可选）

## 环境要求

- PHP >= 7.4（扩展：pdo_mysql, json, mbstring, openssl）
- MySQL 5.7+ / 8.0+
- Composer 2.x

## 快速开始

### 1. 安装依赖

```bash
composer install
npm install && npm run build
```

### 2. 配置环境

```bash
cp .env.example .env
# 编辑 .env，配置数据库等
```

### 3. 初始化数据库

```bash
mysql -u root -p < database/schema.sql
```

### 4. 启动开发服务器

```bash
# 统一入口，默认 8080
composer start

# 或指定端口
php -S localhost:8080 -t public
```

### 5. 测试接口

```bash
# 健康检查
curl http://localhost:8080/ping

# 管理端登录 (admin / password)
curl -X POST http://localhost:8080/admin/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"password"}'

# 小程序 API 配置
curl http://localhost:8080/api/config
```

## 目录结构

```
sports-api/
├── app/
│   ├── Controllers/     # 控制器
│   │   ├── Admin/       # 管理端
│   │   ├── Api/         # 小程序 API
│   │   ├── Common/      # 公共
│   │   └── Unit/        # 单位端
│   ├── Handlers/        # 错误处理
│   ├── Middleware/      # 中间件
│   ├── Models/          # 模型（待扩展）
│   ├── Services/        # 服务
│   └── helpers.php      # 辅助函数
├── config/              # 配置
├── database/            # 数据库
├── docs/                # 文档
├── public/              # Web 根目录
├── storage/logs/        # 日志
└── bootstrap/           # 引导
```

## 路由说明

### 管理端 `/admin`

| 方法 | 路径 | 说明 | 鉴权 |
|------|------|------|------|
| GET | /admin/login | 登录页 | 否 |
| POST | /admin/auth/login | 登录 | 否 |
| GET | /admin/dashboard | 仪表盘 | 否 |
| GET | /admin/users | 用户列表 | 是 |

### 单位端 `/unit`

| 方法 | 路径 | 说明 | 鉴权 |
|------|------|------|------|
| POST | /unit/auth/login | 登录 | 否 |
| GET | /unit/dashboard | 仪表盘 | 否 |
| GET | /unit/profile | 单位信息 | 是 |

### 小程序 API `/api`

| 方法 | 路径 | 说明 | 鉴权 |
|------|------|------|------|
| POST | /api/auth/wxlogin | 微信登录 | 否 |
| GET | /api/config | 全局配置 | 否 |
| GET | /api/user/info | 用户信息 | 是 |

## 鉴权说明

需要鉴权的接口需在请求头携带：

```
Authorization: Bearer <token>
```

获取 token：
- 管理端：`POST /admin/auth/login`，返回 `{ "data": { "token": "..." } }`
- 单位端：`POST /unit/auth/login`，返回 `{ "data": { "token": "..." } }`
- 小程序：`POST /api/auth/wxlogin`，传入 `code`（wx.login 获取）

## 响应格式

统一 JSON：

```json
{
  "code": 0,
  "message": "success",
  "data": { ... }
}
```

错误：

```json
{
  "code": 401,
  "message": "错误描述",
  "data": null
}
```

## 共享组件
管理端/单位端共用：分页、筛选、CRUD 基类、批量操作、OSS 上传、弹窗、表格等。详见 [COMPONENTS.md](COMPONENTS.md)。

## 云端减脂计划 - 扩展文档

| 文档 | 说明 |
|------|------|
| [FEATURES.md](FEATURES.md) | 功能需求规格：多单位/单元、打卡任务、积分、奖品兑换等 |
| [DATA_MODEL.md](DATA_MODEL.md) | 数据模型设计：表结构、实体关系、积分规则 JSON |
| [ROADMAP.md](ROADMAP.md) | 开发路线图与任务拆解：5 阶段、接口清单、交付物 |

- 管理端页面: http://localhost:8080/admin/login（admin / password）
- 单位端页面: http://localhost:8080/unit/login（unit1 / secret）

## 扩展开发

### 新增路由

在 `config/routes.php` 对应分组中追加。

### 新增控制器

在 `app/Controllers/{Admin|Unit|Api}/` 下创建类，实现对应方法。

### 新增模型

在 `app/Models/` 下继承 `Illuminate\Database\Eloquent\Model`。

### 新增中间件

在 `app/Middleware/` 下实现 `Psr\Http\Server\MiddlewareInterface`。

## 部署

1. 将 `public` 设为 Web 根目录
2. 配置 Nginx/Apache 将请求转发到 `public/index.php`
3. 确保 `storage` 可写
4. 生产环境设置 `APP_DEBUG=false`，更换 `APP_KEY`、`JWT_SECRET`

---

更新日期：2025-01
