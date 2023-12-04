<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendMailIfBankAccountChanged extends Mailable
{
    use Queueable, SerializesModels;


    public $accountNumber;

    public function __construct($acoountDetials)
    {
        $this->acoountDetials = $acoountDetials;
    }


    public function build()
    {
        return $this->markdown('email-templates.bankAccountChange', [
            'acoountDetials' => $this->acoountDetials,
        ]);
    }

}
