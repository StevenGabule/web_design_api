<?php


	namespace App\Repositories\Eloquent;


	use App\Models\Design;
  use App\Repositories\Contracts\IDesign;

  class DesignRepository extends BaseRepository implements IDesign
	{
    public function model(): string
    {
      return Design::class;
    }

    public function addComment($designId, array $data)
    {
      return $this->find($designId)->comments()->create($data);
    }

    public function like($id)
    {
      $design = $this->model->findOrFail($id);
      if ($design->isLikedByUser(auth()->id())) $design->unlike();
      else $design->like();
      return $design->likes()->count();
    }

    public function isLikedByUser($id)
    {
      $design = $this->model->findOrFail($id);
      return $design->isLikedByUser(auth()->id());
    }

  }
