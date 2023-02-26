<?php

namespace App\Policies;

use App\Models\Design;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class DesignPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Design $design
     * @return Response|bool
     */
    public function view(User $user, Design $design)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Design $design
     * @return Response|bool
     */
    public function update(User $user, Design $design): Response|bool
    {
        return $user->id === $design->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Design $design
     * @return Response|bool
     */
    public function delete(User $user, Design $design): Response|bool
    {
      return $user->id === $design->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Design $design
     * @return Response|bool
     */
    public function restore(User $user, Design $design): Response|bool
    {
      return $user->id === $design->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Design $design
     * @return Response|bool
     */
    public function forceDelete(User $user, Design $design): Response|bool
    {
      return $user->id === $design->user_id;
    }
}
