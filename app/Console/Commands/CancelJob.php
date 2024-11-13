<?php

namespace App\Console\Commands;

use App\Models\CustomJob;
use Illuminate\Console\Command;

class CancelJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:cancel {pid : The process ID to cancel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel a running command by sending a SIGTERM signal';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pid = $this->argument('pid');

        if (!is_numeric($pid)) {
            $this->error('The PID must be a number.');
            return 1;
        }

        $runningPid = CustomJob::where(
            ['pid',$pid],['status', CustomJob::RUNNING ]
            )->pluck('pid')->first();

        if (!$runningPid || !posix_kill($pid, 0)) {
            $this->error("Process with PID $pid does not exist.");
            return 1;
        }

        if ($runningPid && posix_kill($pid, SIGTERM)) {
            $this->info("SIGTERM signal sent to process $pid.");
            return 0;
        } else {
            $this->error("Failed to send SIGTERM signal to process $pid.");
            return 1;
        }
    }
}
