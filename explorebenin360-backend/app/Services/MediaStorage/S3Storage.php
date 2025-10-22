<?php

namespace App\Services\MediaStorage;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class S3Storage implements MediaStorage
{
    protected string $disk;
    protected ?string $cdnBaseUrl;

    public function __construct(string $disk = 's3')
    {
        $this->disk = $disk;
        $this->cdnBaseUrl = env('CDN_BASE_URL');
    }

    public function upload(UploadedFile $file, array $options = []): array
    {
        $path = ($options['path'] ?? 'media') . '/' . uniqid('', true) . '-' . $file->getClientOriginalName();
        $path = str_replace(' ', '-', $path);
        Storage::disk($this->disk)->put($path, file_get_contents($file->getRealPath()), 'public');

        $url = $this->url($path);

        return [
            'url' => $url,
            'width' => null,
            'height' => null,
            'mime' => $file->getMimeType(),
            'size_bytes' => $file->getSize(),
            'provider' => 's3',
            'metadata' => ['path' => $path],
        ];
    }

    public function url(string $pathOrId, array $options = []): string
    {
        $url = Storage::disk($this->disk)->url($pathOrId);
        if ($this->cdnBaseUrl) {
            $parsed = parse_url($url);
            $replaced = rtrim($this->cdnBaseUrl, '/') . ($parsed['path'] ?? '');
            return $replaced;
        }
        return $url;
    }
}
