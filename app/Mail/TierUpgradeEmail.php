<?php

namespace App\Mail;

use App\Models\MembershipTier;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TierUpgradeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public MembershipTier $newTier
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Congratulations! You've Been Upgraded to {$this->newTier->name} Tier",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tier-upgrade',
        );
    }
}
