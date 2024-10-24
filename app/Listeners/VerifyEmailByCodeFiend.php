<?php

namespace App\Listeners;

use App\Events\VerifyEmailByCode;
use App\Mail\SendActiveCode;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class VerifyEmailByCodeFiend
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(VerifyEmailByCode $event): void
    {
        $users = $event->user;

        Mail::to($users->email)->send(new SendActiveCode(
            __('main.active_account', [
                'type' => __('main.email')
            ]), __('main.code_msg_active', ['code' => $users->email_code, 'name' => $users->name])));
    }
}

