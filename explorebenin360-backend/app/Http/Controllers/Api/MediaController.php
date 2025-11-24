<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\MediaStorage\MediaStorage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'model_type' => ['nullable', 'string', 'max:100'],
            'model_id' => ['nullable', 'integer', 'min:1'],
            'type' => ['nullable', 'in:image,video,panorama,document'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = Media::query();

        if ($request->filled('model_type')) {
            $query->where('model_type', $data['model_type']);
        }

        if ($request->filled('model_id')) {
            $query->where('model_id', $data['model_id']);
        }
        
        if ($request->filled('type')) {
            $query->where('type', $data['type']);
        }

        $perPage = $data['per_page'] ?? 15;
        return response()->json($query->paginate($perPage));
    }

    public function show(int $id)
    {
        $media = Media::findOrFail($id);
        return response()->json($media);
    }

    public function store(Request $request, MediaStorage $storage)
    {
        $this->authorize('create', Media::class);

        $data = $request->validate([
            'model_type' => ['nullable', 'string', 'max:100'],
            'model_id' => ['nullable', 'integer', 'min:1'],
            'files' => ['required_without:file', 'array', 'max:10'],
            'files.*' => ['file', 'max:' . (config('media.max_size_mb') * 1024)],
            'file' => ['required_without:files', 'file', 'max:' . (config('media.max_size_mb') * 1024)],
            'type' => ['nullable', Rule::in(['image', 'video', 'panorama', 'document'])],
            'alt' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string'],
        ]);

        $maxSizeMb = (int) Config::get('media.max_size_mb', 15);
        $allowedMimes = Config::get('media.allowed_mimes');

        $validator = Validator::make($request->all(), [
            'type' => ['nullable', Rule::in(['image', 'video', 'panorama', 'document'])],
            'model_type' => ['nullable', 'string', 'max:100'],
            'model_id' => ['nullable', 'integer', 'min:1'],
            'alt' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string'],
        ]);
        $validator->after(function ($v) use ($request, $allowedMimes, $maxSizeMb) {
            $files = $this->extractFiles($request);
            if (empty($files)) {
                $v->errors()->add('files', 'No files provided.');
                return;
            }
            if (count($files) > 10) {
                $v->errors()->add('files', 'Too many files. Maximum is 10.');
            }
            foreach ($files as $file) {
                if ($file->getSize() > $maxSizeMb * 1024 * 1024) {
                    $v->errors()->add('files', 'File exceeds maximum size of ' . $maxSizeMb . 'MB.');
                }
                if (!in_array($file->getMimeType(), $allowedMimes)) {
                    $v->errors()->add('files', 'Invalid file type: ' . $file->getMimeType());
                }
            }
        });
        $validator->validate();

        $files = $this->extractFiles($request);
        $uploaded = [];

        foreach ($files as $file) {
            $upload = $storage->upload($file, []);

            $media = Media::create([
                'model_type' => $request->string('model_type'),
                'model_id' => $request->integer('model_id') ?: null,
                'type' => $request->string('type') ?: $this->inferType($file->getMimeType()),
                'url' => $upload['url'],
                'provider' => $upload['provider'],
                'alt' => $request->string('alt') ?: $file->getClientOriginalName(),
                'caption' => $request->string('caption'),
                'width' => $upload['width'],
                'height' => $upload['height'],
                'size_bytes' => $upload['size_bytes'],
                'mime' => $upload['mime'],
                'metadata_json' => $upload['metadata'] ?? [],
                'created_by' => Auth::id(),
            ]);

            $uploaded[] = $media;
        }

        return response()->json($uploaded, 201);
    }

    public function destroy(int $id)
    {
        $media = Media::findOrFail($id);
        $this->authorize('delete', $media);
        $media->delete();
        return response()->json(['status' => 'deleted']);
    }

    protected function extractFiles(Request $request): array
    {
        $files = [];
        if ($request->hasFile('file')) {
            $f = $request->file('file');
            $files = is_array($f) ? $f : [$f];
        }
        if ($request->hasFile('files')) {
            $f = $request->file('files');
            $files = array_merge($files, is_array($f) ? $f : [$f]);
        }
        return array_filter($files, fn($f) => $f instanceof UploadedFile);
    }

    protected function inferType(?string $mime): string
    {
        if (!$mime) return 'document';
        if (str_starts_with($mime, 'image/')) return 'image';
        if (str_starts_with($mime, 'video/')) return 'video';
        return 'document';
    }
}
