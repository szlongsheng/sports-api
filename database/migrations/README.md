# 数据库迁移

迁移已汇总至 `../schema.sql`，使用 `make db` 执行即可。

schema.sql 支持重复执行：CREATE TABLE IF NOT EXISTS + 存储过程按需添加缺失字段。
