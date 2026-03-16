<?php

namespace App\Mail;

use App\Models\FamilyInvite;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\URL;

class FamilyInviteMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public FamilyInvite $invite;
    public string $acceptUrl;
    public string $inviterName;

    /**
     * Create a new message instance.
     */
    public function __construct(FamilyInvite $invite, string $inviterName)
    {
        $this->invite = $invite->load('family');
        $this->inviterName = $inviterName;

        $expiresAt = $invite->expires_at ?? now()->addDays(7);

        $this->acceptUrl = URL::temporarySignedRoute(
            'user.families.invite.accept',
            $expiresAt,
            ['token' => $invite->token]
        );
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this
            ->from(
                config('mail.from.address'),
                config('mail.from.name')
            )
            ->subject('You’ve been invited to join ' . $this->invite->family->name)
            ->view('emails.family_invite')
            ->with([
                'invite'      => $this->invite,
                'family'      => $this->invite->family,
                'acceptUrl'   => $this->acceptUrl,
                'inviterName' => $this->inviterName,
            ]);
    }
}
