<?php

namespace App\Events;

use App\Logging\JobLog;
use App\Models\CustomJob;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Log;

class EventJobCanceled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public CustomJob $customJob)
    {

        {
            $customJob->status = CustomJob::CANCELED;
            $customJob->description = "CANCELED";
            $customJob->save();
            JobLog::info( $customJob);
        }
     //   $job = unserialize($customJob->payload);
       // Log::channel(channel: 'background_jobs')->info('Job[' . $customJob->pid . '] - [' . $job->class .'::'. $job->method.'] - Status: Canceled' );
    }       

}