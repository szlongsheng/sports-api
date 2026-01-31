-- =============================================================================
-- 体育 API + 云端减脂计划 - 完整数据库结构（可重复执行）
-- =============================================================================
-- 兼容版本: MySQL 5.7+ / 8.0+
-- 字符集: utf8mb4（支持 emoji 及完整 Unicode）
-- 执行: mysql -u root -p < database/schema.sql  或  make db
-- =============================================================================

CREATE DATABASE IF NOT EXISTS `sports_api` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sports_api`;

-- -----------------------------------------------------------------------------
-- 管理员表 (admins)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '主键 ID',
  `username` varchar(50) NOT NULL COMMENT '登录用户名',
  `password` varchar(255) NOT NULL COMMENT 'bcrypt 加密密码',
  `name` varchar(50) DEFAULT NULL COMMENT '显示名称',
  `status` tinyint DEFAULT 1 COMMENT '1启用 0禁用',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员表';

-- -----------------------------------------------------------------------------
-- 单位表 (units)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `units` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '主键 ID',
  `account` varchar(50) NOT NULL COMMENT '单位登录账号',
  `password` varchar(255) NOT NULL COMMENT 'bcrypt 加密密码',
  `name` varchar(100) NOT NULL COMMENT '单位名称',
  `contact` varchar(50) DEFAULT NULL COMMENT '联系人',
  `phone` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `status` tinyint DEFAULT 1 COMMENT '1启用 0禁用',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account` (`account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='单位表';

-- -----------------------------------------------------------------------------
-- 小程序用户表 (users)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '主键 ID',
  `openid` varchar(64) NOT NULL COMMENT '微信 openid',
  `unionid` varchar(64) DEFAULT NULL COMMENT '微信 unionid',
  `nickname` varchar(50) DEFAULT NULL COMMENT '微信昵称',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像 URL',
  `phone` varchar(20) DEFAULT NULL COMMENT '手机号',
  `unit_id` int unsigned DEFAULT NULL COMMENT '所属单位 ID',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `openid` (`openid`),
  KEY `unit_id` (`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='小程序用户表';

-- -----------------------------------------------------------------------------
-- 单元表 (campaigns)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `campaigns` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '主键 ID',
  `unit_id` int unsigned NOT NULL COMMENT '所属单位 ID',
  `name` varchar(100) NOT NULL COMMENT '单元名称',
  `start_at` datetime DEFAULT NULL COMMENT '活动开始时间',
  `end_at` datetime DEFAULT NULL COMMENT '活动结束时间',
  `points_rules` json DEFAULT NULL COMMENT '积分规则配置 JSON',
  `points_multiplier` decimal(5,2) DEFAULT 1.00 COMMENT '积分阈值上浮系数',
  `exchange_review` tinyint DEFAULT 1 COMMENT '兑换审核 1需审核 0无需审核',
  `invite_code` varchar(20) DEFAULT NULL COMMENT '邀请码',
  `detail_content` longtext DEFAULT NULL COMMENT '活动详情正文 HTML',
  `detail_images` json DEFAULT NULL COMMENT '详情图集 URL 列表',
  `rules_content` longtext DEFAULT NULL COMMENT '活动规则富文本 HTML',
  `splash_image` varchar(512) DEFAULT NULL COMMENT '启动页背景图 URL',
  `status` tinyint DEFAULT 1 COMMENT '1启用 0禁用',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `unit_id` (`unit_id`),
  KEY `status` (`status`),
  KEY `invite_code` (`invite_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='单元/活动表';

-- -----------------------------------------------------------------------------
-- 单元成员表 (campaign_members)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `campaign_members` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '主键 ID',
  `campaign_id` int unsigned NOT NULL COMMENT '单元 ID',
  `user_id` int unsigned NOT NULL COMMENT '用户 ID',
  `points` int DEFAULT 0 COMMENT '当前积分（冗余）',
  `joined_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_campaign_user` (`campaign_id`, `user_id`),
  KEY `user_id` (`user_id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='单元成员表';

-- -----------------------------------------------------------------------------
-- 自定义运动表 (campaign_custom_tasks)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `campaign_custom_tasks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '主键 ID',
  `campaign_id` int unsigned NOT NULL COMMENT '单元 ID',
  `type` varchar(20) NOT NULL DEFAULT 'custom' COMMENT '任务类型 steps微信步数/weight体重/video视频/custom自定义',
  `name` varchar(50) NOT NULL COMMENT '运动名称 如 微信步数、记录体重、跳绳',
  `cover_image` varchar(512) DEFAULT NULL COMMENT '封面图 URL',
  `description` text DEFAULT NULL COMMENT '运动描述',
  `unit` varchar(20) DEFAULT '次' COMMENT '计量单位 步/kg/次 等',
  `points_rule` json DEFAULT NULL COMMENT '积分规则',
  `need_media` tinyint DEFAULT 0 COMMENT '是否需上传视频/图片 0否 1是',
  `need_review` tinyint DEFAULT 0 COMMENT '需 media 时是否审核 0否 1是',
  `sort` int DEFAULT 0,
  `status` tinyint DEFAULT 1 COMMENT '1启用 0禁用',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `campaign_id` (`campaign_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='自定义运动表';

-- -----------------------------------------------------------------------------
-- 打卡记录表 (check_ins)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `check_ins` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '主键 ID',
  `campaign_id` int unsigned NOT NULL COMMENT '单元 ID',
  `user_id` int unsigned NOT NULL COMMENT '用户 ID',
  `task_id` int unsigned NOT NULL COMMENT '任务 ID，关联 campaign_custom_tasks.id',
  `value` json DEFAULT NULL COMMENT '打卡数据（步数、体重、次数等）',
  `media_url` varchar(512) DEFAULT NULL COMMENT '视频/图片 URL（video 类型必填）',
  `points` int DEFAULT 0,
  `status` tinyint DEFAULT 1 COMMENT '0待审核 1通过 2拒绝',
  `checked_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_campaign_user_task_date` (`campaign_id`, `user_id`, `task_id`, `checked_at`),
  KEY `campaign_id` (`campaign_id`),
  KEY `user_id` (`user_id`),
  KEY `task_id` (`task_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='打卡记录表';

-- -----------------------------------------------------------------------------
-- 积分流水表 (points_logs)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `points_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '主键 ID',
  `campaign_id` int unsigned NOT NULL COMMENT '单元 ID',
  `user_id` int unsigned NOT NULL COMMENT '用户 ID',
  `type` varchar(20) NOT NULL COMMENT 'checkin/exchange/adjust',
  `amount` int NOT NULL,
  `ref_type` varchar(30) DEFAULT NULL,
  `ref_id` int unsigned DEFAULT NULL,
  `remark` varchar(200) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_campaign_user_created` (`campaign_id`, `user_id`, `created_at`),
  KEY `campaign_id` (`campaign_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='积分流水表';

-- -----------------------------------------------------------------------------
-- 奖品表 (prizes)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `prizes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '主键 ID',
  `campaign_id` int unsigned NOT NULL COMMENT '单元 ID',
  `name` varchar(100) NOT NULL COMMENT '奖品名称',
  `image` varchar(512) DEFAULT NULL COMMENT '图片 URL',
  `images` json DEFAULT NULL COMMENT '轮播图 URL 列表',
  `description` longtext DEFAULT NULL COMMENT '详情富文本 HTML',
  `points_required` int unsigned NOT NULL COMMENT '所需积分（基础值）',
  `stock` int unsigned DEFAULT 0 COMMENT '库存',
  `per_user_limit` int unsigned DEFAULT 0 COMMENT '每人限兑次数，0不限',
  `sort` int DEFAULT 0 COMMENT '排序',
  `status` tinyint DEFAULT 1 COMMENT '1上架 0下架',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `campaign_id` (`campaign_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='奖品表';

-- -----------------------------------------------------------------------------
-- 兑换记录表 (exchanges)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `exchanges` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '主键 ID',
  `campaign_id` int unsigned NOT NULL COMMENT '单元 ID',
  `user_id` int unsigned NOT NULL COMMENT '用户 ID',
  `prize_id` int unsigned NOT NULL COMMENT '奖品 ID',
  `points_used` int unsigned NOT NULL,
  `status` varchar(20) DEFAULT 'pending' COMMENT '待发货 pending=待发货 shipped=已发货 done=已完成',
  `sign_image` varchar(512) DEFAULT NULL COMMENT '签名图片 URL',
  `reviewer_id` int unsigned DEFAULT NULL COMMENT '审核人 ID',
  `ship_no` json DEFAULT NULL COMMENT '多个单号,json数组[{"no":"1234567890","name":"奖品名称"}]',
  `shipped_at` datetime DEFAULT NULL COMMENT '发货时间',
  `address` json DEFAULT NULL COMMENT '收货地址,json数组[{"name":"张三","phone":"13800138000","address":"北京市海淀区"}]',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_campaign_user_status` (`campaign_id`, `user_id`, `status`),
  KEY `campaign_id` (`campaign_id`),
  KEY `user_id` (`user_id`),
  KEY `prize_id` (`prize_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='兑换记录表';

-- -----------------------------------------------------------------------------
-- 健康资讯表 (health_info)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `health_info` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '主键 ID',
  `campaign_id` int unsigned DEFAULT NULL COMMENT '所属单元 ID，NULL 为平台全局',
  `title` varchar(100) NOT NULL COMMENT '标题',
  `cover_image` varchar(512) DEFAULT NULL COMMENT '封面图 URL',
  `content` longtext DEFAULT NULL COMMENT '正文 HTML（图文混排）',
  `category_id` int unsigned DEFAULT NULL COMMENT '分类 ID',
  `sort` int DEFAULT 0,
  `status` tinyint DEFAULT 1 COMMENT '1展示 0隐藏',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `campaign_id` (`campaign_id`),
  KEY `category_id` (`category_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='健康资讯表';

-- -----------------------------------------------------------------------------
-- 健康资讯分类表 (health_info_categories)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `health_info_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '主键 ID',
  `name` varchar(100) NOT NULL COMMENT '分类名称',
  `sort` int DEFAULT 0,
  `status` tinyint DEFAULT 1 COMMENT '1展示 0隐藏'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='健康资讯分类表';

-- =============================================================================
-- 增量升级：为已有表补充缺失字段（IF 不存在才添加）
-- =============================================================================
DROP PROCEDURE IF EXISTS `add_column_if_not_exists`;
DELIMITER //
CREATE PROCEDURE `add_column_if_not_exists`(
  IN p_table VARCHAR(64),
  IN p_column VARCHAR(64),
  IN p_definition VARCHAR(1000)
)
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = p_table AND COLUMN_NAME = p_column
  ) THEN
    SET @sql = CONCAT('ALTER TABLE `', p_table, '` ADD COLUMN `', p_column, '` ', p_definition);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END IF;
END //
DELIMITER ;

-- users
CALL add_column_if_not_exists('users', 'unit_id', "int unsigned DEFAULT NULL COMMENT '所属单位 ID' AFTER `phone`");

-- campaigns
CALL add_column_if_not_exists('campaigns', 'detail_content', "longtext DEFAULT NULL COMMENT '活动详情正文 HTML' AFTER `invite_code`");
CALL add_column_if_not_exists('campaigns', 'detail_images', "json DEFAULT NULL COMMENT '详情图集 URL 列表' AFTER `detail_content`");
CALL add_column_if_not_exists('campaigns', 'rules_content', "longtext DEFAULT NULL COMMENT '活动规则富文本 HTML' AFTER `detail_images`");
CALL add_column_if_not_exists('campaigns', 'splash_image', "varchar(512) DEFAULT NULL COMMENT '启动页背景图 URL' AFTER `rules_content`");

-- check_ins（新结构：task_id 替代 task_type+custom_task_id）
CALL add_column_if_not_exists('check_ins', 'task_id', "int unsigned DEFAULT NULL COMMENT '任务 ID' AFTER `user_id`");

-- campaign_custom_tasks
CALL add_column_if_not_exists('campaign_custom_tasks', 'type', "varchar(20) NOT NULL DEFAULT 'custom' COMMENT 'steps/weight/video/custom' AFTER `campaign_id`");
CALL add_column_if_not_exists('campaign_custom_tasks', 'cover_image', "varchar(512) DEFAULT NULL COMMENT '封面图 URL' AFTER `name`");
CALL add_column_if_not_exists('campaign_custom_tasks', 'description', "text DEFAULT NULL COMMENT '运动描述' AFTER `cover_image`");

-- 添加 unit_id 索引（若列存在但索引不存在）
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND INDEX_NAME = 'unit_id');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE `users` ADD KEY `unit_id` (`unit_id`)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

DROP PROCEDURE IF EXISTS `add_column_if_not_exists`;

-- prizes.description 改为 longtext 支持富文本
SET @col_type = (SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'prizes' AND COLUMN_NAME = 'description');
SET @sql = IF(@col_type = 'text', 'ALTER TABLE `prizes` MODIFY COLUMN `description` longtext DEFAULT NULL COMMENT ''详情富文本 HTML''', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- 初始数据
-- -----------------------------------------------------------------------------
-- 管理员账号: admin / password
INSERT INTO `admins` (`username`, `password`, `name`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '超级管理员')
ON DUPLICATE KEY UPDATE `username`=`username`;

-- 单位账号: unit1 / secret
INSERT INTO `units` (`account`, `password`, `name`, `status`) VALUES
('unit1', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', '测试单位', 1)
ON DUPLICATE KEY UPDATE `account`=`account`;
