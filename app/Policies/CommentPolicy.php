<?php

  namespace App\Policies;

  use App\Models\Comment;
  use App\Models\User;
  use Illuminate\Auth\Access\HandlesAuthorization;
  use Illuminate\Auth\Access\Response;

  class CommentPolicy
  {
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Comment $comment
     * @return Response|bool
     */
    public function update(User $user, Comment $comment): Response|bool
    {
      return $user->id === $comment->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Comment $comment
     * @return Response|bool
     */
    public function delete(User $user, Comment $comment): Response|bool
    {
      return $user->id === $comment->user_id;
    }

    public function restore(User $user, Comment $comment): Response|bool
    {
      return $user->id === $comment->user_id;
    }


    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Comment $comment
     * @return Response|bool
     */
    public function forceDelete(User $user, Comment $comment): Response|bool
    {
      return $user->id === $comment->user_id;
    }
  }
