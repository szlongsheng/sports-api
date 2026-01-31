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
        $files = $request->getUploadedFiles();
        $file = $files['file'] ?? $files['image'] ?? (is_array($files) ? reset($files) : null);

        if (!$file || !$file instanceof UploadedFile || $file->getError() !== UPLOAD_ERR_OK) {
            $response->getBody()->write(json_encode(json_error(400, '请选择要上传的文件')));
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
}
