<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomJob extends Model
{

    const ERROR = 1;
    const SUCCESS = 0;
    const RUNNING = 2;
    const QUEUED = 3;
    const CANCELED = SIGTERM;

    /** @use HasFactory<\Database\Factories\CustomJobFactory> */
    use HasFactory;
    protected $table = 'custom_jobs';
    protected $fillable = ['pid', 'priority', 'status', 'description','payload', 'attempts', 'finished_at'];
}
