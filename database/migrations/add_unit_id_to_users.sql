-- 为 users 表增加 unit_id 列（已有库可执行）
-- 新库可直接使用 schema.sql

ALTER TABLE `users`
  ADD COLUMN `unit_id` int unsigned DEFAULT NULL COMMENT '所属单位 ID' AFTER `phone`,
  ADD KEY `unit_id` (`unit_id`);
