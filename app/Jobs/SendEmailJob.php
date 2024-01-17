<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $subject;
    protected $body;
    protected $mailService;
    protected $recipientEmail;
    protected $recipientName;

    // protected $queue = 'mails_queue';

    public function __construct($data)
    {
        $this->subject = $data['subject'];
        $this->body = $data['body'];
        $this->recipientName = $data['recipient_name'];
        $this->recipientEmail = $data['recipien_email'];
    }

    public function handle()
    {
        $this->recipientEmail = 'vishaldudalkar.infanion@gmail.com';

        Mail::to($this->recipientEmail, $this->recipientName)->send(new SendMail($this->subject, $this->body));
    }
}
