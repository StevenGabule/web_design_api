<?php

  namespace App\Http\Controllers;

  use App\Http\Resources\UserResource;
  use App\Repositories\Contracts\IUser;
  use EagerLoad;
  use Illuminate\Http\Request;
  use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

  class UserController extends Controller
  {
    private IUser $users;

    public function __construct(IUser $users)
    {
      $this->users = $users;
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
      $users = $this->users->withCriteria([
        new EagerLoad(['designs'])
      ])->all();
      return UserResource::collection($users);
    }

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function search(Request $request): AnonymousResourceCollection
    {
      $designers = $this->users->search($request);
      return UserResource::collection($designers);
    }

    /**
     * @param $username
     * @return UserResource
     */
    public function findByUsername($username): UserResource
    {
      $user = $this->users->findWhereFirst('username', $username);
      return new UserResource($user);
    }
  }
