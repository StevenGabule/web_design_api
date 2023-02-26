<?php

  namespace App\Jobs;

  use App\Models\Design;
  use Exception;
  use Illuminate\Bus\Queueable;
  use Illuminate\Contracts\Queue\ShouldQueue;
  use Illuminate\Foundation\Bus\Dispatchable;

  use Illuminate\Queue\InteractsWithQueue;
  use Illuminate\Queue\SerializesModels;
  use Illuminate\Support\Facades\Log;
  use Illuminate\Support\Facades\Storage;
  use Intervention\Image\Facades\Image;

  class UploadImage implements ShouldQueue
  {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Design $design;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Design $design)
    {
      $this->design = $design;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $disk = $this->design->disk;
      $filename = $this->design->image;
      $original_image = storage_path().'/uploads/original/'.$filename;

      try {
        Image::make($original_image)->fit(800, 600, function ($constraint) {
          $constraint->aspectRatio();
        })->save($large = storage_path() . "/uploads/large/{$filename}");

        Image::make($original_image)->fit(250, 200, function ($constraint) {
          $constraint->aspectRatio();
        })->save($thumbnail = storage_path() . "/uploads/thumbnail/{$filename}");

        // store images to permanent disk original image
        if (Storage::disk($disk)->put("uploads/designs/original/{$filename}", fopen($original_image, 'r+'))) {
          Storage::delete($original_image);
        }

        if (Storage::disk($disk)->put("uploads/designs/large/{$filename}", fopen($large, 'r+'))) {
          Storage::delete($large);
        }

        if (Storage::disk($disk)->put("uploads/designs/thumbnail/{$filename}", fopen($thumbnail, 'r+'))) {
          Storage::delete($thumbnail);
        }

        // update the database record with success flag
        $this->design->update(['upload_success' => 1]);
      } catch (Exception $e) {
        Log::error($e->getMessage());
      }
    }
  }
