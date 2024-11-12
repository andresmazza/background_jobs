<?php

use Illuminate\Support\Facades\Artisan;

if (!function_exists('runBackgroundJob')) {

    /**
     * Run a job in the background, using the artisan command.
     *
     * @param string $class - The class of the job
     * @param string $method - The method of the job to run
     * @param array $params - The parameters for the job
     * @param array $options - The options for the job, currently only accepts 'delay'
     * @return void
     */
    function runBackgroundJob($class, $method, $params = [], $options = [])
    {
        Log::channel(channel: 'background_jobs')->info('Queue Job: ' . $class . ' - ' . $method . ' - ' . json_encode($params));

        $command = "php " . base_path('artisan') . " job:run-background-job '$class' $method";

        foreach ($params as $param) {
            $command .= " --params=" . escapeshellarg($param);
        }

        if (isset($options['delay'])) {
            $command = "sleep {$options['delay']} && $command";
        }

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            pclose(popen("start /B " . $command, "r"));
        } else {
            exec($command . " > /dev/null 2>&1 &");
        }
    }

 
}
