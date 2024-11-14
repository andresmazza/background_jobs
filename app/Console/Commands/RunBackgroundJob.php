<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Events\EventJobCanceled;
use App\Events\EventJobDone;
use App\Events\EventJobError;
use App\Events\EventJobQueued;
use App\Events\EventJobRun;
use App\Models\CustomJob;

use Exception;

class RunBackgroundJob extends Command
{
    const ERROR = 1;
    const SUCCESS = 0;
    const RUNNING = 2;
    const QUEUED = 3;
    const CANCELED = SIGTERM;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:run {class} {method} {--params=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a background job';

    public $signal;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $class = $this->argument('class');
        $method = $this->argument('method');
        $params = $this->option('params');
        $pid = getmypid();

        $maxRetries = config('background-jobs.max_retries', 3);
        $retryDelay = config('background-jobs.retry_delay', 5); //retry delay in Seconds


        $cj = new CustomJob();
        $cj->pid = $pid;
        $payload = $this->createPayload($class, $method, $params, $maxRetries, $retryDelay);
        $cj->payload = serialize($payload);

        //Queued Job
        EventJobQueued::dispatch($cj);

        $this->info('Job queued [ ' . $pid . ' ]');

        // Validate class and method
        if (!$this->validateClassAndMethod($class, $method)) {

            $errorMessage = 'Unregistered class or method. ' . $class . '::' . $method;

            $this->error($errorMessage);
            $cj->description = $errorMessage;
            EventJobError::dispatch($cj);

            return RunBackgroundJob::ERROR;
        }
        // Create an instance of the class that will be triggered by the JOB
        $instance = app($class);

        // Listen SIGNAL TO CANCEL JOB
        $this->trap([SIGTERM, SIGINT], function (int $signal) {
            $this->signal = $signal;
            return RunBackgroundJob::CANCELED;
        });


        try {
            return retry($maxRetries, function (int $attempt) use ($instance, $cj, $method, $params) {
                $cj->attempts = $attempt;
                if ($this->signal) {
                    EventJobCanceled::dispatch($cj);
                    exit;
                }

                //Launch Job
                EventJobRun::dispatch($cj);

                $instance->$method(...$params);

                EventJobDone::dispatch($cj);

            }, function (int $attempt, $exception) use ($cj, $maxRetries, $retryDelay) {

                EventJobError::dispatch($cj);

                return $retryDelay;
            });
        } catch (Exception $exception) {
            $cj->description = $exception->getMessage();
            EventJobError::dispatch($cj);
        }


    }


    public function validateClassAndMethod($class, $method)
    {
        $class = str_replace('::class', '', $class);

        // For example, check against a whitelist of allowed classes
        $allowedClasses = config('background-jobs.allowed_classes', []);

        return in_array($class, $allowedClasses) && method_exists($class, $method);

    }


    public function createPayload($class, $method, $params, $maxRetries, $retryDelay)
    {
        $payload = new \stdClass();
        $payload->class = $class;
        $payload->method = $method;
        $payload->params = [$params];
        $payload->maxRetries = $maxRetries;
        $payload->retryDelay = $retryDelay;

        return $payload;
    }

}
