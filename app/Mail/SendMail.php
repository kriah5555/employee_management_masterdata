<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $htmlContent;

    /**
     * Create a new message instance.
     *
     * @param string $htmlContent The HTML content of the email
     */
    public function __construct($subject, $htmlContent)
    {
        $this->subject     = $subject;
        $this->htmlContent = $htmlContent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->html($this->htmlContent);
    }
}
