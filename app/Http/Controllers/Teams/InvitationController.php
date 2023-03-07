<?php

  namespace App\Http\Controllers\Teams;

  use Illuminate\Validation\ValidationException;
  use Illuminate\Support\Facades\Mail;
  use App\Http\Controllers\Controller;
  use Illuminate\Auth\Access\AuthorizationException;
  use Illuminate\Http\{JsonResponse, Request, Response};
  use App\Http\Requests\Invitation\ResponseRequest;
  use App\Repositories\Contracts\{IInvitation, ITeam, IUser};
  use App\Mail\SendInvitationToJoinTeam;
  use App\Models\{Team};

  class InvitationController extends Controller
  {

    private IInvitation $invitations;
    private ITeam $teams;
    private IUser $users;

    public function __construct(IInvitation $invitations, ITeam $teams, IUser $users)
    {
      $this->invitations = $invitations;
      $this->teams = $teams;
      $this->users = $users;
    }

    protected function createInvitation(bool $user_exists, Team $team, string $email)
    {
      $invitation = $this->invitations->create([
        'team_id' => $team->id,
        'sender_id' => auth()->id(),
        'recipient_email' => $email,
        'token' => md5(uniqid(microtime()))
      ]);
      Mail::to($email)->send(new SendInvitationToJoinTeam($invitation, $user_exists));
    }

    /**
     * @param Request $request
     * @param $teamId
     * @return JsonResponse
     * @throws ValidationException
     */
    public function invite(Request $request, $teamId): JsonResponse
    {
      $team = $this->teams->find($teamId);
      $this->validate($request, ['email' => 'required|email']);
      $user = auth()->user();

      // ** check if the user owns the team
      if (!$user->isOwnerOfTeam($team)) {
        return response()->json(['error' => 'You are not the team owner'], 422);
      }

      // ** check if the email has a pending invitation
      if ($team->hasPendingInvite($request->input('email'))) {
        return response()->json(['error' => 'Email already has a pending invite.'], 422);
      }

      // ** get the recipient by email
      $recipient = $this->users->findByEmail($request->input('email'));

      if ($team->hasUser($recipient)) {
        return response()->json(['error' => 'This user seems to be a team member already.'], 422);
      }

      // ** if the recipient does not exists, send invitation to join in the team
      $this->createInvitation((bool)$recipient, $team, $request->input('email'));
      return response()->json(['success' => true], 201);
    }

    /**
     * 💡 Resend the invitation
     * @param integer $id invitation id
     * @throws AuthorizationException
     */
    public function resend(int $id): JsonResponse
    {
      $invitation = $this->invitations->find($id);
      $this->authorize('resend', $invitation);
      $recipient = $this->users->findByEmail($invitation->recipient_email);
      Mail::to($invitation->recipient_email)
        ->send(new SendInvitationToJoinTeam($invitation, !is_null($recipient)));
      return response()->json(['success' => true]);
    }

    /**
     * @throws AuthorizationException
     */
    public function respond(ResponseRequest $request, int $id): JsonResponse
    {
      $token = $request->input('token');
      $decision = $request->input('decision'); // 'accept' or 'deny'
      $invitation = $this->invitations->find($id);

      // check if the invitation belongs to this user
      $this->authorize('respond', $invitation);

      // ** check to make sure that the tokens match
      if ($invitation->token !== $token) {
        response()->json(['error' => 'Invalid token.'], 422);
      }

      if ($decision != 'deny') $this->invitations->addUserToTeam($invitation->team, auth()->id());

      $invitation->delete();
      return response()->json(['success' => true], 201);

    }

    /**
     * 💡 Remove invitation request
     * @param integer $id invitation id
     * @throws AuthorizationException
     */
    public function destroy(int $id): JsonResponse
    {
      $invitation = $this->invitations->find($id);
      $this->authorize('delete', $invitation);
      $invitation->delete();
      return response()->json([], 204);
    }

  }
