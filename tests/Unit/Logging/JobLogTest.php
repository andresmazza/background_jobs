<?php

use App\Logging\JobLog;
use App\Models\CustomJob;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    
    $this->customJob = new CustomJob();
    $this->customJob->pid = '12345';
    $this->customJob->description = 'Test job description';
    $this->customJob->payload = serialize((object)[
        'class' => 'App\Jobs\ExampleJob',
        'method' => 'handle',
        'params' => 1,
        'maxRetries' => 3
    ]);
});

it('logs info message correctly', function () {
    
    Log::shouldReceive('channel')
        ->once()
        ->with('background_jobs')
        ->andReturnSelf();

    Log::shouldReceive('info')
        ->once()
        ->with('Job[12345] - [App\Jobs\ExampleJob::handle] - Status: Test job description');

    JobLog::info($this->customJob);
});

it('logs error message correctly', function () {
    Log::shouldReceive('channel')
        ->once()
        ->with('background_jobs_errors')
        ->andReturnSelf();

    Log::shouldReceive('error')
        ->once()
        ->with("Job[12345] - [App\Jobs\ExampleJob::handle] - Status: ERROR - Test job description");

    JobLog::error($this->customJob);
});

it('logs error message with max retries reached', function () {
    $this->customJob->attempts = 3;

    Log::shouldReceive('channel')
        ->once()
        ->with('background_jobs')
        ->andReturnSelf();

    Log::shouldReceive('info')
        ->once()
        ->with("Job[12345] - [App\Jobs\ExampleJob::handle] - Status: ERROR - Test job description - Max Retries Reached");

    Log::shouldReceive('channel')
        ->once()
        ->with('background_jobs_errors')
        ->andReturnSelf();

    Log::shouldReceive('error')
        ->once()
        ->with("Job[12345] - [App\Jobs\ExampleJob::handle] - Status: ERROR - Test job description - Max Retries Reached");

    JobLog::error($this->customJob);
});
