

## About Laravel
This custom background job runner allows you to execute PHP classes as background jobs, independent of Laravel's built-in queue system. It provides scalability, error handling, and ease of use within a Laravel application.


## Usage

### Running a Background Job

To run a background job, use the `runBackgroundJob` function:
```php
runBackgroundJob(ExampleJob::class, 'handle', ['param1', 'param2']);
```

The function takes three parameters:
- `$class`: The fully qualified class name of the job to run.
- `$method`: The name of the method to call on the job class.
- `$params`: An array of parameters to pass to the job method.

//TODO
You can also pass an optional `$options` array to specify additional options:
- `$options['delay']`: The number of seconds to delay the job execution. Defaults to 0.

### Adding Delay
To delay the execution of a job, pass a `delay` option:

```php
 runBackgroundJob(ExampleJob::class, 'handle', ['param1', 'param2'], ['delay' => 60]);


```

This will delay the job execution by 60 seconds.


### Retry Mechanism

The retry mechanism is handled automatically based on the configuration in `config/background-jobs.php`. If a job fails, it will be retried up to `max_retries` times with a delay of `retry_delay` seconds between attempts.

### Job Priority

Currently, job priority is not implemented in this version. Jobs are executed in the order they are received.

## Web-Based Dashboard

A simple web interface is provided to monitor and manage background jobs:

1. Access the dashboard at `/background-jobs`
2. View job logs and error logs
3. Run new jobs directly from the interface


## Security Requirements:

- Only classes listed in the `allowed_classes` configuration can be run as background jobs.
- Class and method names are validated before execution to prevent unauthorized code execution.
  
  1. Update the `config/background-jobs.php` file to add your allowed job classes:
        ```php
        return [
        'allowed_classes' => [
            \App\Jobs\ExampleJob::class,
            // Add other allowed classes here
        ],
            'max_retries' => 3,
            'retry_delay' => 60, // in seconds
        ];

## Logging

- Successful job executions are logged in `storage/logs/background_jobs.log`
- Failed job executions and errors are logged in `storage/logs/background_jobs_errors.log`


## Extending the System

To add new job classes:

1. Create a new class in the `App\Jobs` namespace
2. Implement a `handle` method (or any other method you want to call)
3. Add the class to the `allowed_classes` array in the configuration file


Example:

```php
 namespace App\Jobs;


class NewJob
{
    public function handle($param)
    {
        // Job logic here
    }
}

```

Then add `\App\Jobs\NewJob::class` to the `allowed_classes` array in `config/background-jobs.php`.

## Troubleshooting

- If jobs are not running, check the PHP error logs and Laravel logs for any issues.
- Ensure that the web server has permission to execute the artisan command in the background.
- On Unix-based systems, you may need to adjust the command execution in the `runBackgroundJob` function to use `nohup` or other system-specific commands for proper background execution.

## Configure

### Config logging channels
1. Update the `config/logging.php` file to add the custom log channels for background jobs:

   ```php
   return [
       'channels' => [
        'background_jobs' => [
            'driver' => 'single',
            'path' => storage_path('logs/background_jobs.log'),
            'level' => 'debug',
        ],

        'background_jobs_errors' => [
            'driver' => 'single',
            'path' => storage_path('logs/background_jobs_errors.log'),
            'level' => 'error',
        ],
       ]
    ]


### Config custom jobs
1. Update the `config/background-jobs.php` file to add your allowed job classes:

   ```php
   return [
    'allowed_classes' => [
        \App\Jobs\ExampleJob::class,
        // Add other allowed classes here
    ],
       'max_retries' => 3,
       'retry_delay' => 60, // in seconds
    ];
2. Make sure your `config/logging.php` file includes the custom log channels for background jobs.


### Add cutom helpers
1. Copy the provided files into your Laravel project structure.
2. Add the following line to your `composer.json` file in the `autoload` section:

   ```json
   "files": [
       "app/Helpers/helpers.php"
   ]
3. Run `composer dump-autoload` to register the new helper function.