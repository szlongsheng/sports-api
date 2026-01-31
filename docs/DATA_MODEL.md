# 云端减脂计划 - 数据模型设计

## 一、核心实体关系

```
Admin (全局管理员)
  └── 管理 Unit

Unit (单位)
  └── 拥有多个 Campaign (单元/活动)

Campaign (单元/活动)
  ├── 配置: 活动时间、积分规则、审核开关
  ├── 拥有: 奖品池 Prize
  ├── 包含: 打卡任务 Task
  └── 成员: CampaignMember (用户-单元关系)

User (小程序用户)
  └── 通过 CampaignMember 加入多个 Campaign

CheckIn (打卡记录)
  ├── 关联 User, Campaign, Task
  └── 类型: steps/pushups/running/weight/video

PointsLog (积分流水)
  ├── 关联 User, Campaign
  └── 类型: checkin/exchange/adjust

Prize (奖品)
  └── 归属 Campaign

Exchange (兑换记录)
  ├── 关联 User, Campaign, Prize
  └── 状态流转: pending → approved/rejected → shipped → done
```

---

## 二、表结构设计

### 2.1 现有表（沿用）

- `admins` - 全局管理员
- `units` - 单位
- `users` - 小程序用户（已有 `unit_id`，可扩展为多单元通过中间表）

### 2.2 单元表 campaigns

| 字段 | 类型 | 说明 |
|------|------|------|
| id | int | PK |
| unit_id | int | 所属单位 |
| name | varchar(100) | 单元名称 |
| start_at | datetime | 活动开始时间 |
| end_at | datetime | 活动结束时间 |
| points_rules | json | 积分规则配置 |
| exchange_review | tinyint | 1需审核 0无需审核 |
| detail_content | longtext | 活动详情正文 HTML（图文混排） |
| detail_images | json | 详情图集 URL 列表 |
| rules_content | longtext | 活动规则富文本 HTML |
| splash_image | varchar(512) | 启动页背景图 URL |
| status | tinyint | 1启用 0禁用 |
| created_at | datetime | |
| updated_at | datetime | |

**points_rules**：积分规则现移至各任务的 `points_rule` 字段，campaigns 表保留可作备用或全局默认。

### 2.3 单元成员表 campaign_members

| 字段 | 类型 | 说明 |
|------|------|------|
| id | int | PK |
| campaign_id | int | 单元 ID |
| user_id | int | 用户 ID |
| points | int | 当前积分（冗余，可实时算） |
| joined_at | datetime | 加入时间 |
| created_at | datetime | |
| updated_at | datetime | |

**唯一约束**: (campaign_id, user_id)

### 2.4 运动表 campaign_custom_tasks（统一管理任务类型）

**type 取值**：
- `steps` 微信步数
- `weight` 记录体重
- `video` 运动视频
- `custom` 自定义（如 跳绳、深蹲 等，单元可增删改）

### 2.5 运动表 campaign_custom_tasks

| 字段 | 类型 | 说明 |
|------|------|------|
| id | int | PK |
| campaign_id | int | 单元 ID |
| type | varchar(20) | steps/weight/video/custom |
| name | varchar(50) | 运动名称，如 微信步数、记录体重、跳绳 |
| cover_image | varchar(512) | 封面图 URL |
| description | text | 运动描述 |
| unit | varchar(20) | 计量单位 步/kg/次 等 |
| points_rule | json | 积分规则 |
| need_media | tinyint | 是否需上传视频/图片 0否 1是 |
| need_review | tinyint | 需 media 时是否审核 0否 1是 |
| sort | int | 排序 |
| status | tinyint | 1启用 0禁用 |
| created_at | datetime | |
| updated_at | datetime | |

### 2.6 打卡记录表 check_ins

| 字段 | 类型 | 说明 |
|------|------|------|
| id | int | PK |
| campaign_id | int | 单元 |
| user_id | int | 用户 |
| task_id | int | 任务 ID，关联 campaign_custom_tasks.id |
| value | json | 打卡数据（步数、体重、次数等） |
| media_url | varchar(255) | 视频/图片 URL（video 类型必填） |
| points | int | 本次获得积分 |
| status | tinyint | 0待审核 1通过 2拒绝 |
| checked_at | datetime | 打卡时间 |
| created_at | datetime | |
| updated_at | datetime | |

**索引**: (campaign_id, user_id, task_id, checked_at)

### 2.7 积分流水表 points_logs

| 字段 | 类型 | 说明 |
|------|------|------|
| id | int | PK |
| campaign_id | int | 单元 |
| user_id | int | 用户 |
| type | varchar(20) | checkin/exchange/adjust |
| amount | int | 积分变动（正负） |
| ref_type | varchar(30) | check_in/exchange |
| ref_id | int | 关联 ID |
| remark | varchar(200) | 备注（如调整原因） |
| created_at | datetime | |

**索引**: (campaign_id, user_id, created_at)

### 2.8 奖品表 prizes

| 字段 | 类型 | 说明 |
|------|------|------|
| id | int | PK |
| campaign_id | int | 单元 |
| name | varchar(100) | 奖品名称 |
| image | varchar(255) | 图片 URL |
| points_required | int | 所需积分（基础值） |
| stock | int | 库存 |
| per_user_limit | int | 每人限兑次数，0不限 |
| sort | int | 排序 |
| status | tinyint | 1上架 0下架 |
| created_at | datetime | |
| updated_at | datetime | |

### 2.8 积分阈值配置（单元级）

可在 campaigns 表增加 `points_multiplier` 字段，默认 1.0，兑换时：实际所需 = points_required × points_multiplier。

### 2.9 兑换记录表 exchanges

| 字段 | 类型 | 说明 |
|------|------|------|
| id | int | PK |
| campaign_id | int | 单元 |
| user_id | int | 用户 |
| prize_id | int | 奖品 |
| points_used | int | 消耗积分 |
| status | varchar(20) | pending/approved/rejected/shipped/done |
| sign_image | varchar(255) | 签名图片 URL |
| reviewer_id | int | 审核人（单元管理员） |
| reviewed_at | datetime | 审核时间 |
| reject_reason | varchar(200) | 拒绝原因 |
| ship_no | varchar(50) | 快递单号 |
| shipped_at | datetime | 发货时间 |
| address | json | 收货地址 |
| created_at | datetime | |
| updated_at | datetime | |

**索引**: (campaign_id, user_id, status)

### 2.10 健康资讯表 health_info

| 字段 | 类型 | 说明 |
|------|------|------|
| id | int | PK |
| campaign_id | int | 所属单元 ID，NULL 为平台全局 |
| title | varchar(100) | 标题 |
| cover_image | varchar(512) | 封面图 URL |
| content | longtext | 正文 HTML（图文混排） |
| images | json | 正文配图 URL 列表 |
| category | varchar(50) | 分类 饮食/运动/作息/科普 |
| sort | int | 排序 |
| status | tinyint | 1展示 0隐藏 |
| created_at | datetime | |
| updated_at | datetime | |

**说明**：平台级健康资讯（campaign_id 为 NULL）对所有用户可见；非 NULL 表示该单元专属。

---

## 三、微信步数解密

- 小程序端调用 `wx.getWeRunData()` 获取 `encryptedData`、`iv`
- 后端使用登录时保存的 `session_key` 解密，得到 `stepInfoList`
- 按日期取当日步数，与历史对比去重（同一用户同一天只计一次）

---

## 四、单元与 Unit 的关系

- 当前 `units` 表为「单位」登录账号
- 新增 `campaigns` 表为「单元/活动」
- 一个 Unit 可创建多个 Campaign
- 单元管理员：在 `campaign_members` 中 role=admin，或单独建 unit_admins 表绑定 unit 下的某 user 为管理员

**简化方案**：单元管理员 = 单位账号登录后，管理其 unit 下的所有 campaign。即单位即单元管理主体，不额外区分「单元级管理员」。

**扩展方案**：若需「单位内某人仅管理某几个单元」，可增加 `campaign_admins` 表：(campaign_id, user_id)。

---

更新日期：2025-01
