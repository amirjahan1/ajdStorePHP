<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Category
 */
class CategoryResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'parent_id' => $this->parent_id,
            'product_count' => $this->product_count,
            'subcategories_count' => $this->subcategories_count,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'deleted_at' => $this->deleted_at?->toDateTimeString(),
            // روابط اختیاری
            'parent' => $this->whenLoaded('parent', fn() => new CategoryResource($this->parent)),
            'children' => $this->whenLoaded('children', fn() => CategoryResource::collection($this->children)),        ];
    }
}