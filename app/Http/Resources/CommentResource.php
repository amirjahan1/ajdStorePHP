<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Comment
 */
class CommentResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'parent_id' => $this->parent_id,
            'body' => $this->body,
            'rate' => $this->rate,
            'is_approved' => $this->is_approved,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'deleted_at' => $this->deleted_at?->toDateTimeString(),
            'user' => $this->whenLoaded('user', fn() => new UserResource($this->user)),
            'replies' => $this->whenLoaded('replies', fn() => CommentResource::collection($this->replies)),
            'parent' => $this->whenLoaded('parent', fn() => new CommentResource($this->parent)),
        ];
    }
}