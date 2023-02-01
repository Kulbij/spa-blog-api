<?php

namespace App\Http\Resources\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CategoryResource
 *
 * @package App\Http\Resources
 */
class CategoryResource extends JsonResource
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
            'slug' => $this->slug ?? '',
            'is_enabled' => $this->is_enabled,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'posts' => !empty($this->posts) ? $this->posts->toArray() : null,
        ];
    }
}
