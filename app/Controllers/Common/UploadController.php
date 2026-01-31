<?php

declare(strict_types=1);

namespace App\Controllers\Common;

use App\Services\UploadService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\UploadedFile;

class UploadController
{
    public function image(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->handleUpload($request, $response, true);
    }

    public function file(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->handleUpload($request, $response, false);
    }

    private function handleUpload(ServerRequestInterface $request, ResponseInterface $response, bool $imageOnly): ResponseInterface
    {
        $file = $this->resolveUploadedFile($request);

        if (!$file || !$file instanceof UploadedFile || $file->getError() !== UPLOAD_ERR_OK) {
            $msg = '请选择要上传的文件';
            if ($file instanceof UploadedFile) {
                $code = $file->getError();
                if ($code === UPLOAD_ERR_INI_SIZE || $code === UPLOAD_ERR_FORM_SIZE) {
                    $msg = '文件过大，请上传 5MB 以内的图片';
                }
            }
            $response->getBody()->write(json_encode(json_error(400, $msg)));
            return $response->withStatus(400);
        }

        $maxSize = $imageOnly ? UploadService::maxImageSize() : UploadService::maxFileSize();
        if ($file->getSize() > $maxSize) {
            $response->getBody()->write(json_encode(json_error(400, '文件大小超出限制')));
            return $response->withStatus(400);
        }

        $mediaType = $file->getClientMediaType();
        $allowed = $imageOnly ? UploadService::allowedImageTypes() : UploadService::allowedFileTypes();
        if (!in_array($mediaType, $allowed, true)) {
            $response->getBody()->write(json_encode(json_error(400, '不支持的文件类型')));
            return $response->withStatus(400);
        }

        try {
            $service = new UploadService();
            $dir = $imageOnly ? 'images' : 'files';
            $result = $service->upload($file, $dir);
            $response->getBody()->write(json_encode(json_success($result, '上传成功')));
            return $response;
        } catch (\Throwable $e) {
            $response->getBody()->write(json_encode(json_error(500, $e->getMessage())));
            return $response->withStatus(500);
        }
    }

    private function resolveUploadedFile(ServerRequestInterface $request): ?UploadedFile
    {
        $files = $request->getUploadedFiles();
        $file = $files['file'] ?? $files['image'] ?? $files['upload'] ?? null;

        if (!$file instanceof UploadedFile && is_array($files)) {
            $file = $this->extractFirstValidFile($files);
        }

        // PSR-7 层可能为空时，直接使用 $_FILES（PHP 内置服务器 + 路由时偶发）
        if (!$file instanceof UploadedFile && !empty($_FILES)) {
            $file = $this->createUploadedFileFromFILES();
        }

        return $file;
    }

    private function createUploadedFileFromFILES(): ?UploadedFile
    {
        $keys = ['file', 'image', 'upload'];
        foreach ($keys as $key) {
            if (!empty($_FILES[$key]['tmp_name']) && is_uploaded_file($_FILES[$key]['tmp_name'])) {
                return new UploadedFile(
                    $_FILES[$key]['tmp_name'],
                    $_FILES[$key]['name'] ?? null,
                    $_FILES[$key]['type'] ?? null,
                    (int) ($_FILES[$key]['size'] ?? 0),
                    (int) ($_FILES[$key]['error'] ?? UPLOAD_ERR_NO_FILE),
                    true
                );
            }
        }
        $first = reset($_FILES);
        if (is_array($first) && !empty($first['tmp_name']) && is_uploaded_file($first['tmp_name'])) {
            return new UploadedFile(
                $first['tmp_name'],
                $first['name'] ?? null,
                $first['type'] ?? null,
                (int) ($first['size'] ?? 0),
                (int) ($first['error'] ?? UPLOAD_ERR_NO_FILE),
                true
            );
        }
        return null;
    }

    private function extractFirstValidFile(array $files): ?UploadedFile
    {
        foreach ($files as $item) {
            if ($item instanceof UploadedFile) {
                return $item;
            }
            if (is_array($item)) {
                $found = $this->extractFirstValidFile($item);
                if ($found !== null) {
                    return $found;
                }
            }
        }
        return null;
    }
}
