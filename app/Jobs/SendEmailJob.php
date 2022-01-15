<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
// use App\Mail\TestEmailSend;
// use Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $questions;
    public $email;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $questions)
    {
        $this->email = $email;
        $this->questions = $questions;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Mail::to($this->email)->send(new \App\Mail\ReddotMail($this->questions));
    }
}