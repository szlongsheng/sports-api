<?php

declare(strict_types=1);

namespace App\Services;

use OSS\OssClient;
use OSS\Core\OssException;
use Psr\Http\Message\UploadedFileInterface;

class UploadService
{
    private ?OssClient $client = null;
    private string $bucket;
    private string $endpoint;
    private string $baseUrl;
    private bool $useOss;

    public function __construct()
    {
        $this->bucket = $_ENV['OSS_BUCKET'] ?? '';
        $this->endpoint = $_ENV['OSS_ENDPOINT'] ?? '';
        $this->baseUrl = $_ENV['OSS_BASE_URL'] ?? ''; // 自定义域名，如 https://cdn.example.com
        $this->useOss = !empty($_ENV['OSS_ACCESS_KEY_ID']) && !empty($this->bucket);
    }

    /**
     * 上传文件到 OSS 或本地
     */
    public function upload(UploadedFileInterface $file, string $dir = 'uploads'): array
    {
        $ext = $this->getExtension($file->getClientFilename());
        $filename = date('Ymd') . '/' . uniqid() . ($ext ? '.' . $ext : '');
        $object = $dir . '/' . $filename;

        if ($this->useOss) {
            return $this->uploadToOss($file, $object);
        }
        return $this->uploadToLocal($file, $object);
    }

    private function uploadToOss(UploadedFileInterface $file, string $object): array
    {
        try {
            $client = $this->getOssClient();
            $content = (string) $file->getStream();
            $client->putObject($this->bucket, $object, $content);
            $url = $this->baseUrl ? rtrim($this->baseUrl, '/') . '/' . $object : "https://{$this->bucket}.{$this->endpoint}/{$object}";
            return ['url' => $url, 'path' => $object];
        } catch (OssException $e) {
            throw new \RuntimeException('OSS 上传失败: ' . $e->getMessage());
        }
    }

    private function uploadToLocal(UploadedFileInterface $file, string $object): array
    {
        $baseDir = dirname(__DIR__, 2) . '/public/uploads';
        $fullPath = $baseDir . '/' . $object;
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $file->moveTo($fullPath);
        $url = '/uploads/' . $object;
        return ['url' => $url, 'path' => $object];
    }

    private function getOssClient(): OssClient
    {
        if ($this->client === null) {
            $this->client = new OssClient(
                $_ENV['OSS_ACCESS_KEY_ID'],
                $_ENV['OSS_ACCESS_KEY_SECRET'],
                $this->endpoint,
                true
            );
        }
        return $this->client;
    }

    private function getExtension(?string $filename): string
    {
        if (!$filename) {
            return '';
        }
        $pos = strrpos($filename, '.');
        return $pos !== false ? substr($filename, $pos + 1) : '';
    }

    /**
     * 允许的图片类型
     */
    public static function allowedImageTypes(): array
    {
        return ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    }

    /**
     * 允许的文件类型（图片+常用文档）
     */
    public static function allowedFileTypes(): array
    {
        return array_merge(
            self::allowedImageTypes(),
            ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    public static function maxImageSize(): int
    {
        return 5 * 1024 * 1024; // 5MB
    }

    public static function maxFileSize(): int
    {
        return 10 * 1024 * 1024; // 10MB
    }
}
