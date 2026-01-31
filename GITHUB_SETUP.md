# 推送到 GitHub (szlongsheng)

本地已完成：
- 已提交：`main` 分支含完整项目
- 已创建：`basic-framework` 分支（与 main 同内容，可作为“基本框架”分支）

## 方式一：使用 GitHub CLI（推荐）

1. **安装 gh**（若未安装）：
   ```bash
   # macOS
   brew install gh
   gh auth login
   ```

2. **创建仓库并推送**：
   ```bash
   cd /Users/leo/Desktop/202601/sports-api
   gh repo create szlongsheng/sports-api --public --source=. --remote=origin --push --description "体育 API - 管理端/单位端/小程序后端"
   git push origin basic-framework
   ```

## 方式二：网页建仓后推送

1. 打开 https://github.com/new  
   - Owner 选 `szlongsheng`
   - Repository name: `sports-api`
   - Public，**不要**勾选 “Add a README”

2. 在项目目录执行：
   ```bash
   cd /Users/leo/Desktop/202601/sports-api
   git remote add origin https://github.com/szlongsheng/sports-api.git
   git push -u origin main
   git push origin basic-framework
   ```

完成后仓库地址：**https://github.com/szlongsheng/sports-api**
