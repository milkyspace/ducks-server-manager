<?php

namespace App\Jobs;

use App\Http\Controllers\Business\Servers\IServerController;
use App\Http\Controllers\Business\Servers\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessAddingUser implements ShouldQueue
{
    use Queueable;

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
            $isAdded = $this->server->addUser($this->user);
            if ($isAdded !== true) {
                $this->fail('Не создался пользователь ' . $this->user->getUserName() . '(' . $this->user->getId() . ')' . ' на сервере ' . $this->server->getServer()->getAddress());
                $this->release(10);
            }
        } catch (\Exception $e) {
            $this->release(10);
        }
    }
}
