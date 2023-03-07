<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @property integer $id
 * @property string $name
 * @property string $slug
 */
class TeamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
          'id' => $this->id,
          'name' => $this->name,
          'total_members' => $this->members->count(),
          'slug' => $this->slug,
          'designs' => DesignResource::collection($this->designs),
          'owner' => new UserResource($this->owner),
          'member' => UserResource::collection($this->members)
        ];
    }
}
