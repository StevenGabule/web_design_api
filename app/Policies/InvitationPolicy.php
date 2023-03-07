<?php

  namespace App\Policies;

  use App\Models\Invitation;
  use App\Models\User;
  use Illuminate\Auth\Access\HandlesAuthorization;
  use Illuminate\Auth\Access\Response;

  class InvitationPolicy
  {
    use HandlesAuthorization;

    public function delete(User $user, Invitation $invitation): Response|bool
    {
      return $user->id == $invitation->sender_id;
    }

    public function respond(User $user, Invitation $invitation): Response|bool
    {
      return $user->email === $invitation->recipient_email;
    }

    public function resend(User $user, Invitation $invitation): Response|bool
    {
      return $user->id === $invitation->sender_id;
    }

  }
