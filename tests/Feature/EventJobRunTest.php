<?php

use App\Events\EventJobRun;
use App\Models\CustomJob;


it('The event is created with Queued status', function () {
    // Arrange
    $customJob = CustomJob::factory()->create();
    expect($customJob->status)->toBe(CustomJob::QUEUED);
    new EventJobRun($customJob);

    $customJob->refresh();
    expect($customJob->status)->toBe(CustomJob::RUNNING);
});

it('sets the correct description for first attempt', function () {
    // Arrange
    $customJob = CustomJob::factory()->create([
        'attempts' => 1,
        'payload' => serialize((object)['maxRetries' => 3])
    ]);

    // Act
    new EventJobRun($customJob);

    // Assert
    $customJob->refresh();
    expect($customJob->description)->toBe("RUNNING");
});

it('includes retry information in description for subsequent attempts', function () {
    // Arrange
    $customJob = CustomJob::factory()->create([
        'attempts' => 2,
        'payload' => serialize((object)['maxRetries' => 3])
    ]);

    // Act
    new EventJobRun($customJob);

    // Assert
    $customJob->refresh();
    expect($customJob->description)->toBe("RUNNING retrying (2/3)");
});