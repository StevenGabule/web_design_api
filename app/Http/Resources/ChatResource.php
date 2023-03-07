<?php

  namespace App\Http\Resources;

  use Illuminate\Contracts\Support\Arrayable;
  use Illuminate\Database\Eloquent\Relations\BelongsToMany;
  use Illuminate\Http\Request;
  use Illuminate\Http\Resources\Json\JsonResource;
  use JetBrains\PhpStorm\ArrayShape;
  use JsonSerializable;

  /**
   * @property integer $id
   * @property string $created_at
   * @property string $latest_message
   * @property BelongsToMany $participants
   * @method isUnreadForUser(integer $id)
   */
  class ChatResource extends JsonResource
  {
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    #[ArrayShape(['id' => "int", 'dates' => "array", 'is_unread' => "", 'latest_message' => "\App\Http\Resources\MessageResource", 'participants' => "\Illuminate\Http\Resources\Json\AnonymousResourceCollection"])]
    public function toArray($request): array|JsonSerializable|Arrayable
    {
      return [
        'id' => $this->id,
        'dates' => [
          'created_at_human' => $this->created_at->diffForHumans(),
          'created_at' => $this->created_at
        ],
        'is_unread' => $this->isUnreadForUser(auth()->id()),
        'latest_message' => new MessageResource($this->latest_message),
        'participants' => UserResource::collection($this->participants)
      ];
    }
  }
