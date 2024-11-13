<?php

namespace App\Listeners;

use App\Events\JobCanceled;
use App\Events\JobDone;
use App\Events\JobError;
use App\Events\JobQueued;
use App\Events\JobRunning;
use App\Models\CustomJob;
use Illuminate\Events\Dispatcher;

class JobsEventSubscriber
{
    public function handleQueued(JobQueued $event) {

        $customJob = $event->customJob;
        $customJob->payload = serialize($customJob->payload);
        $customJob->description = 'Queued';
        
        $customJob->save();
    }

    public function handleRunning(JobRunning $event) {
        $customJob = $event->customJob;
        $customJob->status = CustomJob::RUNNING;
        $customJob->description = 'Running';

        $customJob->save();
    }

    public function handleDone(JobDone $event) {
        $customJob = $event->customJob;
        $customJob->status = CustomJob::SUCCESS;
        $customJob->description = 'Done';

        $customJob->save();
    }

    public function handleError(JobError $event) {
        $customJob = $event->customJob;
        $customJob->status = CustomJob::ERROR;
       
        $customJob->save();
    }

    public function handleCancel(JobError $event) {
        $customJob = $event->customJob;
        $customJob->status = CustomJob::CANCELED;
       
        $customJob->save();
    }
 
 
 
   /**
     * Register the listeners for the subscriber.
     *
     * @return array<string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            JobQueued::class     => 'handleQueued',
            JobRunning::class    => 'handleRunning',
            JobDone::class       => 'handleDone',
            JobError::class      => 'handleError',
            JobCanceled::class   => 'handleCancel',
        ];
    }
}

