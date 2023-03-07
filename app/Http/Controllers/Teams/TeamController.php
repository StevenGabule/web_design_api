<?php

  namespace App\Http\Controllers\Teams;

  use App\Http\Controllers\Controller;
  use App\Http\Resources\TeamResource;
  use App\Http\Requests\{StoreTeamRequest, UpdateTeamRequest};
  use App\Repositories\Contracts\{IInvitation, ITeam, IUser};
  use Illuminate\Auth\Access\AuthorizationException;
  use Illuminate\Http\JsonResponse;
  use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
  use Illuminate\Support\Str;

  class TeamController extends Controller
  {

    private ITeam $teams;
    private IUser $users;
    private IInvitation $invitations;

    public function __construct(ITeam $teams, IUser $users, IInvitation $invitations)
    {
      $this->teams = $teams;
      $this->users = $users;
      $this->invitations = $invitations;
    }

    public function findById(int $id): TeamResource
    {
      $team = $this->teams->find($id);
      return new TeamResource($team);
    }

    public function fetchUserTeams() //: AnonymousResourceCollection
    {
      $teams = $this->teams->fetchUserTeams();
      return TeamResource::collection($teams);
    }

    public function findBySlug(string $slug): TeamResource
    {
      $team = $this->teams->findWhereFirst('slug', $slug);
      return new TeamResource($team);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTeamRequest $request
     * @return JsonResponse
     */
    public function store(StoreTeamRequest $request): JsonResponse
    {
      $name = $request->post('name');
      $team = $this->teams->create([
        'owner_id' => auth()->id(),
        'name' => $name,
        'slug' => Str::slug($name)
      ]);
      return response()->json(['success' => true], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTeamRequest $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UpdateTeamRequest $request, int $id): JsonResponse
    {
      $team = $this->teams->find($id);
      $this->authorize('update', $team);
      $name = $request->input('name');
      $this->teams->update($id, [
        'name' => $name,
        'slug' => Str::slug($name)
      ]);
      return response()->json(['success' => true], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(int $id): JsonResponse
    {
      $team = $this->teams->find($id);
      $this->authorize('delete', $team);
      $team->delete();
      return response()->json(['', 204]);
    }

    public function removeFromTeam(int $teamId, int $userId): JsonResponse
    {
      $team = $this->teams->find($teamId);
      $user = $this->users->find($userId);

      if ($user->isOwnerOfTeam($team)) {
        return response()->json(['message' => 'You are the team owner'], 401);
      }

      if (!auth()->user()->isOwnerOfTeam($team) && auth()->id() !== $user->id) {
        return response()->json([
          'message' => 'You cannot do this.'
        ], 401);
      }

      $this->invitations->removeUserFromTeam($team, $userId);
      return response()->json([], 204);
    }
  }
