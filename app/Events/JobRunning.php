<?php

namespace App\Events;

use App\Models\CustomJob;
use Illuminate\Broadcasting\InteractsWithSockets;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Log;

class JobRunning
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public CustomJob $customJob)
    {
        $payload = unserialize($customJob->payload);
        
        Log::channel(channel: 'background_jobs')->info('Job[' . $customJob->pid . '] - Status: Running  ('. $customJob->attempts . '/' . $payload->maxRetries . ')' );

    }

}
