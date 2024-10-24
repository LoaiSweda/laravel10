<?php

namespace App\Listeners;

use App\Events\VerifyMobileByCode;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class VerifyMobileByCodeFind
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
    public function handle(VerifyMobileByCode $event): void
    {
        $users = $event->user;
Mail::to($users->email)->send(new SendActiveCode(
    __('main.active_account',[
        'type' => __('main.email')
    ]), __('main.code_msg_active', ['code'=>$users->mobile_code,'name'=>$users->name])));
    }

}
