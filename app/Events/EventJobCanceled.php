<?php

namespace App\Events;


use App\Models\CustomJob;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventJobCanceled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public CustomJob $customJob)
    {
        $customJob->status = CustomJob::CANCELED;
        $customJob->description = "CANCELED";
        $customJob->save();
    }

}