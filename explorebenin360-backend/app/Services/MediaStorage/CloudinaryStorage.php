<?php

namespace App\Services\MediaStorage;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CloudinaryStorage implements MediaStorage
{
    protected string $cloudName;
    protected string $apiKey;
    protected string $apiSecret;

    public function __construct()
    {
        $cloudinaryUrl = env('CLOUDINARY_URL');
        if ($cloudinaryUrl) {
            // cloudinary://<api_key>:<api_secret>@<cloud_name>
            $parts = parse_url($cloudinaryUrl);
            $this->apiKey = $parts['user'] ?? '';
            $this->apiSecret = $parts['pass'] ?? '';
            $this->cloudName = ltrim($parts['path'] ?? '', '/');
        } else {
            $this->cloudName = (string) env('CLOUDINARY_CLOUD_NAME', '');
            $this->apiKey = (string) env('CLOUDINARY_API_KEY', '');
            $this->apiSecret = (string) env('CLOUDINARY_API_SECRET', '');
        }
    }

    public function upload(UploadedFile $file, array $options = []): array
    {
        $timestamp = time();
        $paramsToSign = [
            'timestamp' => $timestamp,
            'folder' => $options['folder'] ?? 'explorebenin360',
            'resource_type' => 'auto',
        ];

        ksort($paramsToSign);
        $toSign = [];
        foreach ($paramsToSign as $k => $v) {
            if ($v === null || $v === '') continue;
            $toSign[] = $k . '=' . $v;
        }
        $signatureBase = implode('&', $toSign) . $this->apiSecret;
        $signature = sha1($signatureBase);

        $endpoint = "https://api.cloudinary.com/v1_1/{$this->cloudName}/auto/upload";

        $response = Http::asMultipart()->post($endpoint, [
            ['name' => 'file', 'contents' => fopen($file->getRealPath(), 'r'), 'filename' => $file->getClientOriginalName()],
            ['name' => 'api_key', 'contents' => $this->apiKey],
            ['name' => 'timestamp', 'contents' => (string) $timestamp],
            ['name' => 'signature', 'contents' => $signature],
            ['name' => 'folder', 'contents' => (string) $paramsToSign['folder']],
        ]);

        if (!$response->successful()) {
            Log::error('Cloudinary upload failed', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \RuntimeException('Upload failed');
        }

        $data = $response->json();
        $secureUrl = $data['secure_url'] ?? $data['url'] ?? '';
        $width = $data['width'] ?? null;
        $height = $data['height'] ?? null;
        $bytes = $data['bytes'] ?? null;
        $format = $data['format'] ?? null;
        $resourceType = $data['resource_type'] ?? 'image';
        $publicId = $data['public_id'] ?? null;

        $metadata = [
            'public_id' => $publicId,
            'resource_type' => $resourceType,
            'format' => $format,
            'version' => $data['version'] ?? null,
        ];

        return [
            'url' => $secureUrl,
            'width' => $width,
            'height' => $height,
            'mime' => $file->getMimeType(),
            'size_bytes' => $bytes ?? $file->getSize(),
            'provider' => 'cloudinary',
            'metadata' => $metadata,
        ];
    }

    public function url(string $pathOrId, array $options = []): string
    {
        // Build transformation URL if width/quality/format provided
        $base = "https://res.cloudinary.com/{$this->cloudName}/image/upload";
        $transformations = [];
        if (!empty($options['width'])) $transformations[] = 'w_' . (int) $options['width'];
        if (!empty($options['quality'])) $transformations[] = 'q_' . $options['quality'];
        if (!empty($options['format'])) $transformations[] = 'f_' . $options['format'];
        if (!empty($options['dpr'])) $transformations[] = 'dpr_' . $options['dpr'];
        if (!empty($options['crop'])) $transformations[] = 'c_' . $options['crop'];
        if (!empty($options['fit'])) $transformations[] = 'c_fit';
        $t = $transformations ? implode(',', $transformations) . '/' : '';
        return rtrim($base, '/') . '/' . $t . ltrim($pathOrId, '/');
    }
}
