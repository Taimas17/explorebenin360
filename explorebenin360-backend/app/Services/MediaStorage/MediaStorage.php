<?php

namespace App\Services\MediaStorage;

use Illuminate\Http\UploadedFile;

interface MediaStorage
{
    /**
     * @param UploadedFile $file
     * @param array $options
     * @return array{url:string,width:int|null,height:int|null,mime:string|null,size_bytes:int|null,provider:string,metadata:array}
     */
    public function upload(UploadedFile $file, array $options = []): array;

    /**
     * Build a public URL for a stored resource. Used mainly by S3.
     */
    public function url(string $pathOrId, array $options = []): string;
}
