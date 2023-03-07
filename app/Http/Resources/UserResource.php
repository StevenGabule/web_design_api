<?php

  namespace App\Http\Resources;

  use Illuminate\Contracts\Support\Arrayable;
  use Illuminate\Http\Request;
  use Illuminate\Http\Resources\Json\JsonResource;
  use JsonSerializable;

  /**
   * @property integer $id
   * @property string $username
   * @property string $photo_url
   * @property string $format_address
   * @property string $about
   * @property string $location
   * @property string $available_to_hire
   * @property string $created_at
   * @property string $email
   * @method name()
   */
  class UserResource extends JsonResource
  {
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
      return [
        'id' => $this->id,
        'name' => $this->name(),
        'username' => $this->username,
        'photo_url' => $this->photo_url,
        'format_address' => $this->format_address,
        'about' => $this->about,
        'location' => $this->location,
        'available_to_hire' => $this->available_to_hire,
        'create_dates' => [
          'created_at_human' => $this->created_at->diffForHumans(),
          'created_at' => $this->created_at
        ],
        $this->mergeWhen(auth()->check() && auth()->id() == $this->id, [
          'email' => $this->email
        ]),
        'designs' => DesignResource::collection(
          $this->whenLoaded('designs')
        ),
      ];
    }
  }
