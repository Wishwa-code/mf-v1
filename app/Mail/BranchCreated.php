<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BranchCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $branchName;
    public $activationLink;

    public function __construct($branchName, $activationLink)
    {
        $this->branchName = $branchName;
        $this->activationLink = $activationLink;
    }

    public function build()
    {
        return $this->view('emails.branch-created') // View that will be used for the email content
        ->subject('New Branch Created')
            ->with([
                'branchName' => $this->branchName,
                'activationLink' => $this->activationLink,
            ]);
    }
}
