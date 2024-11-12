<?php 


return [
'allowed_classes' => [
    \App\Jobs\ExampleJob::class,
    // Add other allowed classes here
],
    'max_retries' => 4,
    'retry_delay' => 60, // in seconds
];
