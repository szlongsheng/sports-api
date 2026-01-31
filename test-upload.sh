#!/bin/bash
# 测试上传接口
# 用法: ./test-upload.sh [token] [图片路径]
# 示例: ./test-upload.sh      (用 README.md 测试，需 token)
#       ./test-upload.sh YOUR_TOKEN
#       ./test-upload.sh YOUR_TOKEN ./path/to/image.png
TOKEN="${1:-}"
FILE="${2:-./README.md}"
URL="http://localhost:8080/unit/api/upload/image"

echo "上传文件: $FILE -> $URL"
if [ -n "$TOKEN" ]; then
  curl -v -X POST "$URL" -H "Authorization: Bearer $TOKEN" -F "file=@$FILE"
else
  echo "提示: 未提供 token 会返回 401。用法: ./test-upload.sh YOUR_TOKEN [文件]"
  curl -v -X POST "$URL" -F "file=@$FILE"
fi
