<?php

  namespace App\Http\Controllers\Chats;

  use App\Http\Controllers\Controller;
  use App\Http\Controllers\Designs\DesignController;
  use App\Http\Requests\Chat\SendMessageRequest;
  use App\Http\Resources\ChatResource;
  use App\Http\Resources\MessageResource;
  use App\Repositories\Eloquent\Criteria\WithTrashed;
  use App\Repositories\Contracts\{IChat, IMessage};
  use Illuminate\Auth\Access\AuthorizationException;
  use Illuminate\Http\JsonResponse;
  use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

  class ChatController extends Controller
  {
    private IChat $chats;
    private IMessage $messages;

    public function __construct(IChat $chats, IMessage $messages)
    {
      $this->chats = $chats;
      $this->messages = $messages;
    }

    public function sendMessage(SendMessageRequest $request): MessageResource
    {
      $recipient = $request->input('recipient'); // recipient id
      $body = $request->input('body');

      $user = auth()->user();

      // check if there is an existing chat
      // between the auth user and the recipient
      $chat = $user->getChatWithUser($recipient);

      if (!$chat) {
        $chat = $this->chats->create([]);
        $this->chats->createParticipants($chat->id, [$user->id, $recipient]);
      }

      // add the message to the chat
      $message = $this->messages->create([
        'user_id' => $user->id,
        'chat_id' => $chat->id,
        'body' => $body,
        'last_read' => null,
      ]);

      return new MessageResource($message);
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function getUserChats(): AnonymousResourceCollection
    {
      $chats = $this->chats->getUserChats();
      return ChatResource::collection($chats);
    }

    public function getChatMessages($id)
    {
      $messages = $this->messages->withCriteria([new WithTrashed()])->findWhere('chat_id', $id);
      return MessageResource::collection($messages);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function markAsRead($id): JsonResponse
    {
      $chat = $this->chats->find($id);
      $chat->markAsReadForUser(auth()->id());
      return response()->json(['success' => true], 201);
    }

    /**
     * @param $id
     * @throws AuthorizationException
     */
    public function destroyMessage($id)
    {
      $message = $this->messages->find($id);
      $this->authorize('delete', $message);
      $message->delete();
    }

  }
