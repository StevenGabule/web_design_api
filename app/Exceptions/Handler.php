<?php

  namespace App\Exceptions;

  use Illuminate\Auth\Access\AuthorizationException;
  use Illuminate\Database\Eloquent\ModelNotFoundException;
  use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
  use Illuminate\Http\JsonResponse;
  use Illuminate\Http\Response;
  use Throwable;
  use Exception;

  class Handler extends ExceptionHandler
  {
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
      //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
      'current_password',
      'password',
      'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
      $this->reportable(function (Throwable $e) {
        //
      });
    }

    public function render($request, Exception|Throwable $e): Response|JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
      if ($e instanceof AuthorizationException && $request->expectsJson()) {
        return response()->json(['error' => [
          'message' => 'You are not authorized to access this resource.'
        ]], 403);
      }

      if ($e instanceof ModelNotFoundException && $request->expectsJson()) {
        return response()->json(['error' => [
          'message' => 'The resource you are trying to access is not found.'
        ]], 404);
      }

      if ($e instanceof ModelNotDefined && $request->expectsJson()) {
        return response()->json(['error' => [
          'message' => 'No model defined'
        ]], 500);
      }

      return parent::render($request, $e);
    }
  }
