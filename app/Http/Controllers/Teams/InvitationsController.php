<?php

namespace App\Http\Controllers\Teams;

use Mail;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\SendInvitationJoinTeam;
use App\Repositories\Contracts\ITeam;
use App\Repositories\Contracts\IUser;
use App\Repositories\Contracts\IInvitation;

class InvitationsController extends Controller
{
    protected $invitations;
    protected $teams;
    protected $user;

    public function __construct(IInvitation $invitations, ITeam $teams, IUser $user)
    {
        $this->invitations = $invitations;
        $this->teams = $teams;
        $this->user = $user;
    }

    public function invite(Request $request, $teamId)
    {
        // get the team
        $team = $this->teams->find($teamId);

        $this->validate($request, [
            'email' => ['request', 'email']
        ]);

        $user = auth()->user();
        //check if the user owns the team
        if (! $user->isOwnerOfTeam($team)) {
            return response()->json([
                'email' => 'No eres el propietario del Team'
            ], 401);
        }

        //check if the email has a pending invitation
        if ($team->hasPendingInvite($request->email)) {
            return response()->json([
                'email' => 'El email tiene una invitación pendiente'
            ], 422);
        }

        //get the recipient by email
        $recipient = $this->users->findByEmail($request->email);

        //if the recipient does'nt exist, send invitation to join the team
        if (! $recipient) {
            $this->createInvitation(false, $team, $request->email);

            return response()->json([
                'message' => 'Invitación enviada al usuario'
            ], 200);
        }

        //check if the team already has the user
        if ($team->hasUser($recipient)) {
            return response()->json([
                'message' => 'Este usuario ya esta en el team'
             ], 422);
        }

        //send the invitation to the user
        $this->createInvitation(true, $team, $request->email);
        return response()->json([
                'message' => 'Invitación enviada al usuario'
            ], 200);
    }

    public function resent($id)
    {
        # code...
    }

    public function respond(Request $request, $id)
    {
        # code...
    }

    public function destroy($id)
    {
        # code...
    }

    protected function createInvitation(bool $user_exists, Team $team, string $email)
    {
        $invitation = $this->invitations->create([
                'team_id'=>$team->id,
                'sender_id' => auth()->id(),
                'recipient_email'=>$email,
                'token'=>md5(uniqid(microtime()))
            ]);

        Mail::to($email)
                    ->send(new SendInvitationJoinTeam($invitation, $user_exists));
    }
}
