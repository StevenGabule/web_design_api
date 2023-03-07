<?php


	namespace App\Repositories\Eloquent;


	use App\Models\Invitation;
  use App\Repositories\Contracts\IInvitation;

  class InvitationRepository extends BaseRepository implements IInvitation
	{
    public function model(): string
    {
      return Invitation::class;
	  }

    public function addUserToTeam($team, $user_id)
    {
      $team->members()->attach($user_id);
    }

    public function removeUserFromTeam($team, $user_id)
    {
      $team->members()->detach($user_id);
    }
  }
