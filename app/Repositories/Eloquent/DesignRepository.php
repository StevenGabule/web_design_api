<?php


  namespace App\Repositories\Eloquent;


  use App\Models\Design;
  use App\Repositories\Contracts\IDesign;
  use Illuminate\Http\Request;

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

    public function search(Request $request)
    {
      $query = (new $this->model)->newQuery();
      $query->where('is_live', true);

      // *** only designs with comments
      if ($request->has_comments) $query->has('comments');

      // *** return only designs assigned to teams
      if ($request->has_team) $query->has('team');

      // *** search title and description for provided string
      if ($request->q) {
        $query->where(function ($q) use ($request) {
          $q->where('title', 'LIKE', '%' . $request->q . '%')
            ->orWhere('description', 'LIKE', '%' . $request->q . '%');
        });
      }

      if ($request->orderBy == 'likes') {
        $query->withCount('likes')->orderByDesc('likes_count');
      } else {
        $query->latest();
      }

      return $query->with('user')->get();
    }
  }
