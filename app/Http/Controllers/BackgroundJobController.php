<?php

namespace App\Http\Controllers;

use File;
use Illuminate\Http\Request;
use Log;

class BackgroundJobController extends Controller
{
    public function index()
    {
        $jobLogs = $this->getLogContents(storage_path('logs/background_jobs.log'));
        $errorLogs = $this->getLogContents(storage_path('logs/background_jobs_errors.log'));

        return view('background-jobs.index', compact('jobLogs', 'errorLogs'));
    }

    private function getLogContents($path)
    {
        if (File::exists($path)) {
            return array_slice(file($path), -100);
        }
        return [];
    }

    public function run(Request $request)
    {
        $class = $request->input('class');
        $method = $request->input('method');
        $params = $request->input('params', null);
        //$params = explode(',', $params);
        try {
            runBackgroundJob($class, $method, $params);
        } catch (\Exception $e) {
            Log::channel(channel: 'background_jobs')->error('Queue Job: ' . $class . ' - ' . $method . ' - ' . json_encode($params) . ' - '. $e->getMessage());

        }

        return redirect()->route('background-jobs.index')->with('success', 'Job started successfully');
    }

}
