<?php

  namespace App\Policies;

  use App\Models\Team;
  use App\Models\User;
  use Illuminate\Auth\Access\HandlesAuthorization;
  use Illuminate\Auth\Access\Response;

  class TeamPolicy
  {
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Team $team
     * @return Response|bool
     */
    public function update(User $user, Team $team): Response|bool
    {
      return $user->isOwnerOfTeam($team);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Team $team
     * @return Response|bool
     */
    public function delete(User $user, Team $team): Response|bool
    {
      return $user->isOwnerOfTeam($team);
    }
  }
