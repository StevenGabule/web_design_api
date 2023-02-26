<?php

  namespace App\Http\Controllers\Designs;

  use App\Models\Design;
  use App\Http\Controllers\Controller;
  use App\Http\Requests\UpdateDesignRequest;
  use Illuminate\Auth\Access\AuthorizationException;
  use Illuminate\Support\Facades\Storage;
  use Illuminate\Http\{JsonResponse, Response};
  use Illuminate\Support\Str;
  use App\Repositories\Contracts\IDesign;

  class DesignController extends Controller
  {

    protected IDesign $designs;

    public function __construct(IDesign $designs)
    {
      $this->designs = $designs;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
      //
    }


    /**
     * Display the specified resource.
     *
     * @param Design $design
     * @return Response
     */
    public function show(Design $design)
    {
      //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Design $design
     * @return Response
     */
    public function edit(Design $design)
    {
      //
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
        $this->designs->update($design->id,[
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
  }
