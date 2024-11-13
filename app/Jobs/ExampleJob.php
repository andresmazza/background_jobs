<?php

namespace App\Jobs;

class ExampleJob
{
    
    public function handle($param1 = null)
    {
        // Simulate some work
        sleep(3);
        
        // Simulate a potential failure
       // if (rand(0, 1) === 0) {
        if (true) {
            throw new \Exception("Random failure occurred");
        }

        return "Job completed with params: $param1";
    }
}
