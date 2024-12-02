<?php

namespace App\Jobs;

use App\Http\Controllers\Business\Servers\IServerController;
use App\Http\Controllers\Business\Servers\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessUpdatingUser implements ShouldQueue
{
    use Queueable;

    public $timeout = 180;

    /**
     * Create a new job instance.
     */
    public function __construct(private IServerController $server, private User $user)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $isUpdate = $this->server->updateUser($this->user);;
            if ($isUpdate !== true) {
                $this->release(10);
            }
        } catch (\Exception $e) {
            $this->release(10);
        }
    }
}
