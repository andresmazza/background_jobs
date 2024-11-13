<?php

namespace App\Events;

use App\Logging\JobLog;
use App\Models\CustomJob;
use Illuminate\Broadcasting\InteractsWithSockets;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Log;

class EventJobRun
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public CustomJob $customJob)
    {
        $payload = unserialize($customJob->payload);
        $customJob->status = CustomJob::RUNNING;
        $customJob->description = "RUNNING";
        if ($customJob->attempts > 1) {
            $customJob->description .= " retrying (" . $customJob->attempts . "/" . $payload->maxRetries . ")";
        }
        $customJob->save();

    }

}
