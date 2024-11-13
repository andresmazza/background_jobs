<?php

namespace App\Jobs;

use Illuminate\Console\Concerns\InteractsWithIO;

class ExampleJob
{
 
    public function handle($params = 0)
    {
        
        // Simulate some work
        sleep($params);
        
        // Simulate a potential failure
       if (rand(0, 1) === 0) {
            throw new \Exception("Random failure occurred");
        }

        return "Job completed with params: $params";
    }

    
}
