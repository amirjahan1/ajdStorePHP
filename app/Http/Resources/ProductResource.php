<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Product
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'category_id' => $this->category_id,
            'comments_count' => $this->comments_count,
            'ratings_count' => $this->ratings_count,
            'average_rating' => $this->average_rating,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'deleted_at' => $this->deleted_at?->toDateTimeString(),
            'category' => $this->whenLoaded('category', fn() => new CategoryResource($this->category)),
            'comments' => $this->whenLoaded('comments', fn() => CommentResource::collection($this->comments)),
        ];
    }
}