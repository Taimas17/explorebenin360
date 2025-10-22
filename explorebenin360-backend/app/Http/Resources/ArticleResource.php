<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'author_name' => $this->author_name,
            'category' => $this->category,
            'tags' => $this->tags ?? [],
            'cover_image_url' => $this->cover_image_url,
            'status' => $this->status,
            'published_at' => optional($this->published_at)->toISOString(),
        ];
    }
}
