<?php

  namespace App\Mail;

  use App\Models\Invitation;
  use Illuminate\Bus\Queueable;
  use Illuminate\Contracts\Queue\ShouldQueue;
  use Illuminate\Mail\Mailable;
  use Illuminate\Mail\Mailables\Content;
  use Illuminate\Mail\Mailables\Envelope;
  use Illuminate\Queue\SerializesModels;

  class SendInvitationToJoinTeam extends Mailable
  {
    use Queueable, SerializesModels;

    private Invitation $invitation;
    private bool $user_exists;

    /**
     * SendInvitationToJoinTeam constructor.
     * @param Invitation $invitation
     * @param bool $user_exists
     */
    public function __construct(Invitation $invitation, bool $user_exists)
    {
      $this->invitation = $invitation;
      $this->user_exists = $user_exists;
    }

    /**
     * @return SendInvitationToJoinTeam
     */
    public function build(): SendInvitationToJoinTeam
    {
      if ($this->user_exists) {
        $url = config('app.client_url') . '/settings/teams';
        return $this->markdown('emails.invitations.invite-existing-user')
          ->subject('Invitation to join team' . $this->invitation->team->name)
          ->with([
            'invitation' => $this->invitation,
            'url' => $url
          ]);
      } else {
        $url = config('app.client_url') . "/register?invitations={$this->invitation->recipient_email}";
        return $this->markdown('emails.invitations.invite-new-user')
          ->subject('Invitation to join team' . $this->invitation->team->name)
          ->with([
            'invitation' => $this->invitation,
            'url' => $url
          ]);
      }
    }
  }
