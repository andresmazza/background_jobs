<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Log;

class RunBackgroundJob extends Command
{
    const ERROR = 1;
    const SUCCESS = 0;
    const RUNING = 2;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:run-background-job {class} {method} {--params=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a background job';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $class = $this->argument('class');
        $method = $this->argument('method');
        $params = $this->option('params');

        // Validate class and method
        if (!$this->validateClassAndMethod($class, $method)) {

            $errorMessage = 'Unregistered class or method. ' . $class . '::' . $method;
            Log::channel('background_jobs_errors')->error($errorMessage);
            $this->error($errorMessage);

            return RunBackgroundJob::ERROR;
        }

        $instance = app($class);

        $maxRetries = config('background-jobs.max_retries', 3);
        $retryDelay = config('background-jobs.retry_delay', 5); //retry delay in Seconds

        return retry($maxRetries, function (int $attempt) use ($instance, $method, $params) {
            Log::channel(channel: 'background_jobs')->info('Queue Job:');

            $result = $instance->$method(...$params);
            Log::channel(channel: 'background_jobs')->info($result . ' -  attempt:' . $attempt);

        }, function (int $attempt, Exception $exception) use ($retryDelay) {

            Log::channel('background_jobs_errors')->error("Job failed: retry attempt " . $attempt .
                " -  retrun delay: " . $retryDelay . " -  Message:" . $exception->getMessage());
            return $retryDelay;
        });
    }
    private function validateClassAndMethod($class, $method)
    {
        $class = str_replace('::class', '', $class);

        // For example, check against a whitelist of allowed classes
        $allowedClasses = config('background-jobs.allowed_classes', []);

        return in_array($class, $allowedClasses) && method_exists($class, $method);

    }

}
