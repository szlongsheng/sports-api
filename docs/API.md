# API 接口文档

## 基础信息

- **Base URL**: `http://your-domain.com`（开发: `http://localhost:8080`）
- **Content-Type**: `application/json`
- **响应格式**: 统一 JSON

## 通用响应结构

```json
{
  "code": 0,
  "message": "success",
  "data": {}
}
```

- `code`: 0 成功，非 0 失败
- `message`: 提示信息
- `data`: 业务数据

## 鉴权

需登录接口在 Header 中携带：

```
Authorization: Bearer <token>
```

## 管理端接口

### 登录
```
POST /admin/auth/login
Body: {"username":"admin","password":"admin123"}
Response: {"code":0,"message":"success","data":{"token":"xxx"}}
```

### 用户列表（需鉴权）
```
GET /admin/users
Header: Authorization: Bearer <token>
```

## 单位端接口

### 登录
```
POST /unit/auth/login
Body: {"account":"xxx","password":"xxx"}
Response: {"code":0,"message":"success","data":{"token":"xxx"}}
```

### 单位信息（需鉴权）
```
GET /unit/profile
Header: Authorization: Bearer <token>
```

## 小程序 API

### 微信登录
```
POST /api/auth/wxlogin
Body: {"code":"wx_login_code"}
Response: {"code":0,"message":"success","data":{"token":"xxx"}}
```

### 全局配置
```
GET /api/config
```

### 用户信息（需鉴权）
```
GET /api/user/info
Header: Authorization: Bearer <token>
```
