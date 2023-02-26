<?php

	namespace App\Traits;


	use Illuminate\Http\JsonResponse;

  trait ResponseReturn
	{

    /**
     * 💡 Response template for success request
     * @param array $result data that return to the request
     * @param string $message text to be given when success occur
     * @return JsonResponse
     */
    public function sendSuccessResponse(array $result, string $message): JsonResponse
    {
      $response = ['success' => true, 'data' => $result, 'message' => $message];
      return response()->json($response);
    }

    /**
     * 💡 Response template for errors request
     * @param string $error
     * @param array $errorMessages
     * @param int $code
     * @return JsonResponse
     */
    public function sendErrorResponse(string $error, array $errorMessages = [], $code = 404): JsonResponse
    {
      $response = ['success' => false, 'message' => $error];
      if (!empty($errorMessages)) $response['data'] = $errorMessages;
      return response()->json($response, $code);
    }
	}
