<?php

namespace App\Console\Commands;

use App\Events\JobCanceled;
use App\Events\JobDone;
use App\Events\JobError;
use App\Events\JobQueued;
use App\Events\JobRunning;
use App\Models\CustomJob;
use Exception;
use Illuminate\Console\Command;
use Log;

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

    public $pid;
    public $signal;
    public $jobClassName;
    public $jobMethodName;
    public $jobParams;
    public $maxRetries = 2;
    public $retryDelay;
    public $attempt = 1;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $jobClassName = $this->argument('class');
        $jobMethodName = $this->argument('method');
        $jobParams = $this->option('params');
        $pid = getmypid();

        $this->maxRetries = config('background-jobs.max_retries', 3);
        $this->retryDelay = config('background-jobs.retry_delay', 5); //retry delay in Seconds


        $cj = new CustomJob();
        $cj->pid = $this->pid;
        $payload = new \stdClass();
        $payload->class = $this->jobClassName;
        $payload->method = $this->jobMethodName;
        $payload->params = [$this->jobParams];
        $payload->maxRetries = $this->maxRetries;
        $payload->retryDelay = $this->retryDelay;
        $cj->payload = $payload;

        //Queued Job
        JobQueued::dispatch($cj);

        $this->info('Job queued [ ' . $this->pid . ' ]');

        // Validate class and method
        if (!$this->validateClassAndMethod($this->jobClassName, $this->jobMethodName)) {

            $errorMessage = 'Unregistered class or method. ' . $this->jobClassName . '::' . $this->jobMethodName;

            $this->error($errorMessage);
            $cj->description = $errorMessage;
            JobError::dispatch($cj);
            return RunBackgroundJob::ERROR;
        }
        // Create an instance of the class that will be triggered by the JOB
        $instance = app($this->jobClassName);

        $this->trap([SIGTERM, SIGINT], function (int $signal) {
            $this->signal = $signal;
            return RunBackgroundJob::CANCELED;
        });
        $this->maxRetries = config('background-jobs.max_retries', 3);
        $this->retryDelay = config('background-jobs.retry_delay', 5); //retry delay in Seconds

        try {
            return retry($this->maxRetries, function (int $attempt) use ($instance, $cj) {
                $cj->attempts = $attempt;
                if ($this->signal) {
                    JobCanceled::dispatch($cj);
                    exit;
                }

                $method = $this->jobMethodName;
                $params = $this->jobParams;

                JobRunning::dispatch($cj);

                $instance->$method(...$params);

                JobDone::dispatch($cj);

            }, function (int $attempt, $exception) use ($cj) {

                $cj->description = "Retrying (" . $attempt . "/" . $this->maxRetries . ") " . $exception->getMessage();
                $cj->status = CustomJob::ERROR;
                JobError::dispatch($cj);

                return $this->retryDelay;
            });
        } catch (Exception $exception) {
            $cj->status = CustomJob::ERROR;
            $cj->description = $exception->getMessage();
            JobError::dispatch($cj);
        }


    }

    //@TODO remove parameters

    private function validateClassAndMethod($class, $method)
    {
        $class = str_replace('::class', '', $class);

        // For example, check against a whitelist of allowed classes
        $allowedClasses = config('background-jobs.allowed_classes', []);

        return in_array($class, $allowedClasses) && method_exists($class, $method);

    }

}
