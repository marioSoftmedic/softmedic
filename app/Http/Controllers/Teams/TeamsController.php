<?php

namespace App\Http\Controllers\Teams;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Repositories\Contracts\ITeam;

class TeamsController extends Controller
{
    protected $teams;

    public function __construct(ITeam $teams)
    {
        $this->teams = $teams;
    }

    public function index()
    {
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:80', 'unique:teams,name']

        ]);

        //create team in database
        $team = $this->teams->create([
            'owner_id' => auth()->id(),
            'name'=>$request->name,
            'slug'=> Str::slug($request->name)
        ]);

        //current user is inserted as team member using boot method in Team model

        return new TeamResource($team);
    }

    public function update(Request $request, $id)
    {
        $team= $this->teams->find($id);
        $this->authorize('update', $team);

        $this->validate($request, [
            'name'=>['required', 'string', 'max:80', 'unique:teams,name,'.$id]
        ]);


        $team= $this->teams->update($id, [
            'name'=>$request->name,
            'slug'=> Str::slug($request->name)
        ]);

        return new TeamResource($team);
    }

    public function findById($id)
    {
        $team = $this->teams->find($id);

        return new TeamResource($team);
    }

    public function fetchUserTeams()
    {
        $teams = $this->teams->fetchUserTeams();

        return TeamResource::collection($teams);
    }

    public function findBySlug($slug)
    {
        # code...
    }

    public function destroy($id)
    {
        # code...
    }
}
