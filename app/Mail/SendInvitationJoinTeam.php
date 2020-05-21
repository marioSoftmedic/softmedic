<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendInvitationJoinTeam extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;
    public $user_exist;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invitation $invitation, bool $user_exist)
    {
        $this->invitation = $invitation;
        $this->user_exists = $user_exist;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->user_exist) {
            return $this->markdown('emails.invitations.invite-existing-user')
           ->subject('Invitación para ingresar team'. $this->invitation->team->name)
           ->with('invitation', $this->invitation);
        } else {
            return $this->markdown('emails.invitations.invite-new-user');
        }
    }
}
