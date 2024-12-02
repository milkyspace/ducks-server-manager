<?php

namespace App\Jobs;

use App\Http\Controllers\Business\Servers\IServerController;
use App\Http\Controllers\Business\Servers\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessAddingUser implements ShouldQueue
{
    use Queueable;

    public $timeout = 180;

    public $tries = 999;

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
                $this->fail('Не добавился пользователь ' . $this->user->getUserName() . '(' . $this->user->getId() . ') на сервере ' . $this->server->getServer()->getAddress());
                $this->release(10);
            }
        } catch (\Exception $e) {
            $this->fail('Exception Не добавился пользователь ' . $this->user->getUserName() . '(' . $this->user->getId() . ') на сервере ' . $this->server->getServer()->getAddress());
            $this->release(10);
        }
    }

    /**
     * Рассчитать количество секунд ожидания перед повторной попыткой выполнения задания.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [5, 10, 20];
    }
}
