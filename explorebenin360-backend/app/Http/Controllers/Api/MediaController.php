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
        $this->authorize('viewAny', Media::class);
        $query = Media::query();
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->string('model_type'));
        }
        if ($request->filled('model_id')) {
            $query->where('model_id', $request->integer('model_id'));
        }
        return response()->json($query->latest()->paginate(20));
    }

    public function show(Request $request, int $id)
    {
        $media = Media::findOrFail($id);
        $this->authorize('view', $media);
        return response()->json(['data' => $media]);
    }

    public function store(Request $request, MediaStorage $storage)
    {
        $this->authorize('create', Media::class);

        $maxSizeMb = (int) Config::get('media.max_size_mb', 15);
        $allowedMimes = Config::get('media.allowed_mimes');

        $rules = [
            'files' => ['required'],
            'files.*' => [
                'file',
                'max:' . ($maxSizeMb * 1024), // in kilobytes
                Rule::in($allowedMimes),
            ],
            'type' => ['nullable', Rule::in(['image', 'video', 'panorama', 'document'])],
            'model_type' => ['nullable', 'string'],
            'model_id' => ['nullable', 'integer'],
            'alt' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string'],
        ];

        // Manually validate MIME since Laravel's Rule::in isn't for mimetypes on files
        $validator = Validator::make($request->all(), [
            'files' => ['required'],
            'type' => ['nullable', Rule::in(['image', 'video', 'panorama', 'document'])],
            'model_type' => ['nullable', 'string'],
            'model_id' => ['nullable', 'integer'],
            'alt' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string'],
        ]);
        $validator->after(function ($v) use ($request, $allowedMimes, $maxSizeMb) {
            $files = $this->extractFiles($request);
            if (empty($files)) {
                $v->errors()->add('files', 'No files provided.');
                return;
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
