<?php

namespace App\Listeners;

use App\Events\EventJobCanceled;
use App\Events\EventJobDone;
use App\Events\EventJobError;
use App\Events\EventJobQueued;
use App\Events\EventJobRun;
use App\Logging\JobLog;
use Illuminate\Events\Dispatcher;

class JobsEventSubscriber
{
    public function handleQueued(EventJobQueued $event)
    {

        $customJob = $event->customJob;
        JobLog::info($customJob);
    }

    public function handleRunning(EventJobRun $event)
    {
        $customJob = $event->customJob;
        JobLog::info($customJob);
    }

    public function handleDone(EventJobDone $event)
    {
        $customJob = $event->customJob;
        JobLog::info($customJob);
    }

    public function handleError(EventJobError $event)
    {
        $customJob = $event->customJob;
        JobLog::error($customJob);
    }

    public function handleCancel(EventJobCanceled $event)
    {
        $customJob = $event->customJob;
        JobLog::info($customJob);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @return array<string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            EventJobQueued::class   => 'handleQueued',
            EventJobRun::class      => 'handleRunning',
            EventJobDone::class     => 'handleDone',
            EventJobError::class    => 'handleError',
            EventJobCanceled::class => 'handleCancel',
        ];
    }
}

