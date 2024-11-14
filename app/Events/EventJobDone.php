<?php

namespace App\Events;

use App\Logging\JobLog;
use App\Models\CustomJob;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Log;

class EventJobDone
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public CustomJob $customJob)
    {
        // $job = unserialize($customJob->payload);
        // Log::channel(channel: 'background_jobs')->info('Job[' . $customJob->pid . '] - [' . $job->class .'::'. $job->method.'] - Status: Done' );

        $customJob->status=CustomJob::SUCCESS;
        $customJob->description="SUCCESS";
        $customJob->save();
        //JobLog::info( $customJob);

    }

}
