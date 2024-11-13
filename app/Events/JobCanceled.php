<?php

namespace App\Events;

use App\Models\CustomJob;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Log;

class JobCanceled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public CustomJob $customJob)
    {
        Log::channel(channel: 'background_jobs')->info('Job[' . $customJob->pid . '] - Status: Canceled' );
    }       

}
