-- =============================================================================
-- 体育 API 数据库结构
-- =============================================================================
-- 兼容版本: MySQL 5.7+ / 8.0+
-- 字符集: utf8mb4（支持 emoji 及完整 Unicode）
-- =============================================================================

CREATE DATABASE IF NOT EXISTS `sports_api` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sports_api`;

-- -----------------------------------------------------------------------------
-- 管理员表 (admins)
-- -----------------------------------------------------------------------------
-- 后台管理系统管理员账号，用于登录管理后台
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '主键 ID',
  `username` varchar(50) NOT NULL COMMENT '登录用户名',
  `password` varchar(255) NOT NULL COMMENT 'bcrypt 加密密码',
  `name` varchar(50) DEFAULT NULL COMMENT '显示名称',
  `status` tinyint DEFAULT 1 COMMENT '1启用 0禁用',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员表';

-- -----------------------------------------------------------------------------
-- 单位表 (units)
-- -----------------------------------------------------------------------------
-- 体育赛事参与单位/机构，如学校、俱乐部等
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `units` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '主键 ID',
  `account` varchar(50) NOT NULL COMMENT '单位登录账号',
  `password` varchar(255) NOT NULL COMMENT 'bcrypt 加密密码',
  `name` varchar(100) NOT NULL COMMENT '单位名称',
  `contact` varchar(50) DEFAULT NULL COMMENT '联系人姓名',
  `phone` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `status` tinyint DEFAULT 1 COMMENT '1启用 0禁用',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account` (`account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='单位表';

-- -----------------------------------------------------------------------------
-- 小程序用户表 (users)
-- -----------------------------------------------------------------------------
-- 微信小程序端用户，通过 openid 关联微信账号
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '主键 ID',
  `openid` varchar(64) NOT NULL COMMENT '微信 openid',
  `unionid` varchar(64) DEFAULT NULL COMMENT '微信 unionid（同主体下多应用统一）',
  `nickname` varchar(50) DEFAULT NULL COMMENT '微信昵称',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像 URL',
  `phone` varchar(20) DEFAULT NULL COMMENT '手机号',
  `unit_id` int unsigned DEFAULT NULL COMMENT '所属单位 ID',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `openid` (`openid`),
  KEY `unit_id` (`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='小程序用户表';

-- -----------------------------------------------------------------------------
-- 初始数据
-- -----------------------------------------------------------------------------
-- 插入测试管理员 (密码: admin123，首次部署可运行 php -r "echo password_hash('admin123',PASSWORD_BCRYPT);" 生成新哈希替换)
INSERT INTO `admins` (`username`, `password`, `name`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '超级管理员')
ON DUPLICATE KEY UPDATE `username`=`username`;
