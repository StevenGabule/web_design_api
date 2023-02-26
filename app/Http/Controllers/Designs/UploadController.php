<?php

  namespace App\Http\Controllers\Designs;

  use App\Http\Controllers\Controller;
  use App\Jobs\UploadImage;
  use Illuminate\Http\JsonResponse;
  use Illuminate\Http\Request;
  use Illuminate\Validation\ValidationException;

  class UploadController extends Controller
  {
    /**
     * 💡 before creating a new design, designer need to upload image
     * @route /api
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function upload(Request $request): JsonResponse
    {
      $this->validate($request, ['image' => 'required|mimes:jpeg,gif,bmp,png|max:2048']);
      $image = $request->file('image');
      $image->getFilename();

      // ** Get the original file name and replace any with business Card.png = timestamp()_business_card.png
      $filename = time().'_'.preg_replace('/\s+/', '_', strtolower($image->getClientOriginalName()));
      $tmp = $image->storeAs('uploads/original', $filename, 'tmp');

      $design = auth()->user()->designs()->create([
        'image' => $filename,
        'disk' => config('site.upload_disk')
      ]);

      $this->dispatch(new UploadImage($design));
      return response()->json(['success' => true], 201);
    }
  }
