<?php

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
    function runBackgroundJob($class, $method, $params = null, $options = [])
    {
        $command = "php " . base_path('artisan') . " job:run '$class' $method";
        if ($params) {
            $command .= " --params=" . escapeshellarg($params);
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
