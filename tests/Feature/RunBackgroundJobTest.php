<?php

use App\Console\Commands\RunBackgroundJob;
use App\Events\EventJobDone;
use App\Events\EventJobQueued;
use App\Events\EventJobRun;
use App\Jobs\ExampleJob;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;
use Mockery;

beforeEach(function () {

    $this->command = new RunBackgroundJob();
});

it('Queues a job and dispatches EventJobQueued', function () {

    Event::fake();
    Config::set('background-jobs.allowed_classes', ['App\Jobs\ExampleJob']);

    $this->artisan('job:run', [
        'class' => 'App\Jobs\ExampleJob',
        'method' => 'handle',
        '--params' => ['0']  //0 seconds for test
    ])->assertExitCode(RunBackgroundJob::SUCCESS);

    Event::assertDispatched(EventJobQueued::class);

});

it('validates class and method', function () {
    Config::set('background-jobs.allowed_classes', ['App\Jobs\ExampleJob']);

    $result = $this->command->validateClassAndMethod('App\Jobs\ExampleJob', 'handle');
    expect($result)->toBeTrue();

    $result = $this->command->validateClassAndMethod('App\Jobs\InvalidJob', 'handle');
    expect($result)->toBeFalse();
});

it('creates a valid payload', function () {
    $payload = $this->command->createPayload('App\Jobs\ExampleJob', 'handle', 0, 3, 5);

    expect($payload)->toBeObject()
        ->class->toBe('App\Jobs\ExampleJob')
        ->method->toBe('handle')
        ->params->toBe([0])
        ->maxRetries->toBe(3)
        ->retryDelay->toBe(5);
});

it('handles job execution and dispatches events', function () {
    Event::fake();

    Config::set('background-jobs.allowed_classes', ['App\Jobs\ExampleJob']);

    // Mock the job class
    $mockJob = Mockery::mock(ExampleJob::class);

    $mockJob->shouldReceive('handle')->once()->andReturn(null);
    $this->app->instance('App\Jobs\ExampleJob', $mockJob);

    $this->artisan('job:run', [
        'class' => 'App\Jobs\ExampleJob',
        'method' => 'handle',
    ])->assertExitCode(RunBackgroundJob::SUCCESS);

    Event::assertDispatched(EventJobQueued::class);
    Event::assertDispatched(EventJobRun::class);
    Event::assertDispatched(EventJobDone::class);
});
