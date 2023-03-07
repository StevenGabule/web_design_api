<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class DesignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
          'id' => $this->id,
          'title' => $this->title,
          'slug' => $this->slug,
          'images' => $this->images,
          'is_live' => $this->is_live,
          'description' => $this->description,
          'likes_count' => $this->likes()->count(),
          'created_at_dates' => [
            'created_at_human' => $this->created_at->diffForHumans(),
            'created_at' => $this->created_at
          ],
          'updated_at_dates' => [
            'updated_at_human' => $this->updated_at->diffForHumans(),
            'updated_at' => $this->updated_at
          ],
          'team' => $this->team ? [
            'id' => $this->team->id,
            'name' => $this->team->name,
            'slug' => $this->team->slug
          ] : null,
          'comments_count' => $this->comments()->count(),
          'comments' => CommentResource::collection($this->whenLoaded('comments')),
          'user' => new UserResource($this->whenLoaded('user'))
        ];
    }
}
