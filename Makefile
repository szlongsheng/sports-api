# =============================================================================
# 体育 API - 常用命令
# =============================================================================
# 使用: make [目标]
# 查看: make help
# =============================================================================

.PHONY: help install start start-admin start-unit start-api dev build env db clean

# 默认显示帮助
help:
	@echo "体育 API - 常用命令"
	@echo ""
	@echo "   make install     安装依赖 (composer + npm)"
	@echo "   make env         复制 .env.example 为 .env"
	@echo "   make start       启动开发服务器 (http://localhost:8080)"
	@echo "   make start-admin 管理端 (http://localhost:8081)"
	@echo "   make start-unit  单位端 (http://localhost:8082)"
	@echo "   make start-api   小程序 API (http://localhost:8083)"
	@echo "   make dev         监听并编译前端 (Tailwind --watch)"
	@echo "   make build       编译前端 (Tailwind minify)"
	@echo "   make clean       清理依赖与构建缓存"
	@echo ""

# 安装依赖
install:
	composer install
	npm install

# 启动开发服务器（默认 8080，三端同源）
# 上传限制：图片 5MB，需覆盖 PHP 默认 2MB
start:
	php -d upload_max_filesize=10M -d post_max_size=12M -S localhost:8080 -t public public/router.php



# 前端开发：监听并编译 CSS
dev:
	npm run dev

# 前端构建：压缩 CSS
build:
	npm run build

# 清理
clean:
	rm -rf vendor node_modules
	@echo "已删除 vendor 与 node_modules"
