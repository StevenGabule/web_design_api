<?php

  namespace App\Http\Resources;

  use Illuminate\Contracts\Support\Arrayable;
  use Illuminate\Http\Request;
  use Illuminate\Http\Resources\Json\JsonResource;
  use JetBrains\PhpStorm\ArrayShape;
  use JsonSerializable;

  /**
   * @property integer $id
   * @property string $body
   * @property string $created_at
   * @method trashed()
   */
  class MessageResource extends JsonResource
  {
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    #[ArrayShape(['id' => "int", 'body' => "string", 'deleted' => "", 'dates' => "array"])]
    public function toArray($request): array|JsonSerializable|Arrayable
    {
      return [
        'id' => $this->id,
        'body' => $this->body,
        'deleted' => $this->trashed(),
        'dates' => [
          'created_at_human' => $this->created_at->diffForHumans(),
          'created_at' => $this->created_at
        ],
      ];
    }

  }
