<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;

class TestMailController extends Controller
{
    public function sendTestMail()
    {

        $htmlContent = '<p>Your HTML content goes here</p>';

        Mail::to('sunilgangadhar.infanion@gmail.com')->send(new SendMail('Test mails', $htmlContent));

        return "Test mail sent successfully!";
    } 
}
