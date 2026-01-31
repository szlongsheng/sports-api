<?php
/**
 * 上传诊断：直接访问此文件，不经过 Slim
 * 访问: curl -X POST http://localhost:8080/test-upload.php -F "file=@./README.md"
 */
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? null,
    'CONTENT_TYPE' => $_SERVER['CONTENT_TYPE'] ?? null,
    'FILES_empty' => empty($_FILES),
    'FILES' => $_FILES,
    'POST_empty' => empty($_POST),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
