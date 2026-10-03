<?php

namespace App\Mail;

use App\Models\FoodDistribution;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DistributionDispatchedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public FoodDistribution $distribution
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Food Package Has Been Dispatched - #' . $this->distribution->distribution_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.distribution-dispatched',
        );
    }
}
