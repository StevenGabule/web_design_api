<?php

  namespace App\Http\Controllers\Designs;

  use App\Http\Resources\DesignResource;
  use App\Models\Design;
  use App\Http\Controllers\Controller;
  use App\Http\Requests\UpdateDesignRequest;
  use App\Repositories\Eloquent\Criteria\ForUser;
  use App\Repositories\Eloquent\Criteria\IsLive;
  use App\Repositories\Eloquent\Criteria\LatestFirst;
  use EagerLoad;
  use Illuminate\Auth\Access\AuthorizationException;
  use Illuminate\Support\Facades\Storage;
  use Illuminate\Http\{JsonResponse, Resources\Json\AnonymousResourceCollection, Response, Request};
  use Illuminate\Support\Str;
  use App\Repositories\Contracts\IDesign;

  class DesignController extends Controller
  {

    /**
     * @var IDesign
     */
    protected IDesign $designs;

    /**
     * DesignController constructor.
     * @param IDesign $designs
     */
    public function __construct(IDesign $designs)
    {
      $this->designs = $designs;
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
      $designs = $this->designs->withCriteria([
        new LatestFirst(),
        new IsLive(),
        new ForUser(2),
        new EagerLoad(['user', 'comments'])
      ])->all();
      return DesignResource::collection($designs);
    }

    /**
     * @param int $id
     * @return DesignResource
     */
    public function findDesign(int $id): DesignResource
    {
      $design = $this->designs->find($id);
      return new DesignResource($design);
    }

    /**
     * @param string $slug
     * @return DesignResource
     */
    public function findBySlug(string $slug): DesignResource
    {
      $design = $this->designs->withCriteria([
        new IsLive(),
        new EagerLoad(['user', 'comments'])
      ])->findWhereFirst('slug', $slug);
      return new DesignResource($design);
    }

    /**
     * @param int $teamId
     * @return AnonymousResourceCollection
     */
    public function getForTeam(int $teamId): AnonymousResourceCollection
    {
      $designs = $this->designs
                    ->withCriteria([new IsLive()])
                    ->findWhere('team_id', $teamId);
      return DesignResource::collection($designs);
    }

    /**
     * @param int $userId
     * @return AnonymousResourceCollection
     */
    public function getForUser(int $userId): AnonymousResourceCollection
    {
      $designs = $this->designs->findWhere('user_id', $userId);
      return DesignResource::collection($designs);
    }

    /**
     * @param int $id
     * @return DesignResource
     */
    public function userOwnsDesign(int $id): DesignResource
    {
      $design = $this->designs->withCriteria([new ForUser(auth()->id())])->findWhereFirst('id', $id);
      return new DesignResource($design);
    }

    /**
     * 💡 When user done uploading the image then user can add more information about the design
     * @route /api/design/update/{design}
     * @param UpdateDesignRequest $request
     * @param Design $design
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UpdateDesignRequest $request, Design $design): JsonResponse
    {
      $this->authorize('update', $design);

      if ($request->wantsJson()) {
        $this->designs->update($design->id, [
          'team_id' => @$request->post('team'),
          'title' => $request->post('title'),
          'description' => $request->post('description'),
          'slug' => Str::slug($request->post('title')),
          'is_live' => !$design->upload_success ? false : $request->post('is_live')
        ]);
        return response()->json(['success' => true], 201);
      }
    }

    /**
     * Remove the specified resource from storage.
     * @param Design $design
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Design $design): JsonResponse
    {
      $this->authorize('delete', $design);
      $this->designs->delete($design->id);
      return response()->json([], 204);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param integer $design
     * @return JsonResponse
     */
    public function restore(int $design): JsonResponse
    {
      $this->designs->restore($design);
      return response()->json(['success' => true], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param integer $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function forceDelete(int $id): JsonResponse
    {
      $design = $this->designs->find($id);
      $this->authorize('forceDelete', $design);
      foreach (['thumbnail', 'large', 'original'] as $size) {
        $disk = $design->disk;
        $file_path = "uploads/designs/{$size}/{$design->image}";
        if (Storage::disk($disk)->exists($file_path)) {
          Storage::disk($disk)->delete($file_path);
        }
      }
      $this->designs->forceDelete($id);
      return response()->json([], 204);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function like(int $id): JsonResponse
    {
      $total = $this->designs->like($id);
      return response()->json([
        'message' => 'Successful',
        'total' => $total
      ]);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function checkIfUserHasLiked(int $id): JsonResponse
    {
      $isLiked = $this->designs->isLikedByUser($id);
      return response()->json(['liked' => $isLiked]);
    }

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function search(Request $request): AnonymousResourceCollection
    {
      $designs = $this->designs->search($request);
      return DesignResource::collection($designs);
    }
  }
