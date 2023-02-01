<?php

namespace App\Http\Resources\Blog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PostResource
 *
 * @package App\Http\Resources
 */
class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? '',
            'description' => $this->description ?? '',
            'is_enabled' => $this->is_enabled,
            'is_published' => $this->is_published,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'category_id' => $this->category->id ?? null,
        ];
    }
}
