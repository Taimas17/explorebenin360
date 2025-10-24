<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $plain = trim(strip_tags((string) ($this->excerpt ?: $this->body)));
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
            'seo' => [
                'title' => $this->title . ' — ExploreBenin360',
                'description' => mb_substr($plain, 0, 160),
                'image' => $this->cover_image_url,
                'path' => '/blog/' . $this->slug,
            ],
        ];
    }
}
