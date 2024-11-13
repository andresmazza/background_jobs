<?php
namespace App\Logging;

use App\Models\CustomJob;
use Log;


class JobLog
{
    /**
     * Customize the given logger instance.
     */
    public static function info(CustomJob $customJob): void
    {
        $job = unserialize($customJob->payload);
        $message = 'Job[' . $customJob->pid . '] - [' . $job->class . '::' . $job->method . '] - Status: ' . $customJob->description . '';

        Log::channel(channel: 'background_jobs')->info($message);
    }

    public static function error(CustomJob $customJob): void
    {

        $job = unserialize($customJob->payload);
        $message = 'Job[' . $customJob->pid . '] - [' . $job->class . '::' . $job->method . '] - Status: ERROR - ' . $customJob->description . '';
        if ($customJob->attempts >= $job->maxRetries) {
            $message .= ' - Max Retries Reached';
            Log::channel(channel: 'background_jobs')->info($message);
        }
        Log::channel(channel: 'background_jobs_errors')->error($message);
    }
}