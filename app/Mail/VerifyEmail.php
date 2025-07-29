<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $pendingUserId;


    public function __construct($token, $pendingUserId)
    {
        $this->token = $token;
        $this->pendingUserId = $pendingUserId;
    }

    public function build()
    {

        return $this->view('emails.verify')->with(['token' => $this->token, 'pendingUserId' => $this->pendingUserId]);
    }

}