<?php

namespace App\Events;

use App\Models\CustomJob;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Log;

class JobError
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public CustomJob $customJob)
    {
        if ($customJob->attempts >= 3) {
            Log::channel(channel: 'background_jobs')->info('Job[' . $customJob->pid . '] - Status: Error - ' . $customJob->description);    
        }
        Log::channel(channel: 'background_jobs_errors')->error('Job[' . $customJob->pid . '] - Status: Error - ' . $customJob->description);
    }


}
