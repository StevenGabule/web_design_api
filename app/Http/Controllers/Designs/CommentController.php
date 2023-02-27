<?php

  namespace App\Http\Controllers\Designs;

  use App\Http\Controllers\Controller;
  use App\Models\Comment;
  use Illuminate\Auth\Access\AuthorizationException;
  use Illuminate\Http\JsonResponse;
  use Illuminate\Http\Request;
  use Illuminate\Http\Response;
  use Illuminate\Validation\ValidationException;
  use App\Http\Requests\{StoreCommentRequest, UpdateCommentRequest};
  use App\Repositories\Contracts\{IComment, IDesign};

  class CommentController extends Controller
  {

    protected IDesign $designs;
    protected IComment $comments;

    public function __construct(IDesign $designs, IComment $comment)
    {
      $this->designs = $designs;
      $this->comments = $comment;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCommentRequest $request
     * @param integer $designId
     * @return JsonResponse
     */
    public function store(StoreCommentRequest $request, int $designId): JsonResponse
    {
      if ($request->wantsJson()) {
        $this->designs->addComment($designId, [
          'body' => $request->post('body'),
          'user_id' => auth()->id(),
        ]);
        return response()->json(['success' => true], 201);
      }
      return response()->json(['success' => false], 400);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param integer $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
      $comment = $this->comments->find($id);
      $this->authorize('update', $comment);
      $this->validate($request, ['body' => 'required']);
      $this->comments->update($id, ['body' => $request->input('body')]);
      return response()->json(['success' => true], 201);
    }

    /**
     * Remove the specified resource from storage.
     * @param integer $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(int $id): JsonResponse
    {
      $comment = $this->comments->find($id);
      $this->authorize('delete', $comment);
      $this->comments->delete($id);
      return response()->json([], 204);
    }

    /**
     * @throws AuthorizationException
     */
    public function restore(int $id): JsonResponse
    {
      $comment = $this->comments->find($id);
      $this->authorize('restore', $comment);
      $this->comments->restore($id);
      return response()->json(['success' => true], 201);
    }

    /**
     * @throws AuthorizationException
     */
    public function forceDelete(int $id): JsonResponse
    {
      $comment = $this->comments->find($id);
      $this->authorize('forceDelete', $comment);
      $this->comments->forceDelete($id);
      return response()->json([], 204);
    }
  }
