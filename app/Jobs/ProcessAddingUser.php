<?php

namespace App\Jobs;

use App\Http\Controllers\Business\Servers\AmneziaServerController;
use App\Http\Controllers\Business\Servers\IServerController;
use App\Http\Controllers\Business\Servers\User;
use App\Http\Controllers\Business\Servers\XuiServerController;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessAddingUser implements ShouldQueue
{
    use Queueable;

    public $timeout = 180;

    public $tries = 5;

    /**
     * Create a new job instance.
     */
    public function __construct(private \App\Http\Controllers\Business\Servers\Server $server, private User $user)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            foreach ([XuiServerController::class] as $controller) {
                /** @var IServerController $controller */
                if ($controller::TYPE === $this->server->getType()) {
                    $server = new $controller($this->server);
                    $isAdded = $server->addUser($this->user);
                    if ($isAdded !== true) {
                        sleep(5);
                        $this->fail('Не добавился пользователь ' . $this->user->getUserName() . '(' . $this->user->getId() . ') на сервере ' . $server->getServer()->getAddress());
                        $this->release(10);
                    }
                }
            }
        } catch (\Exception $e) {
            sleep(5);
            $this->fail('Exception Не добавился пользователь ' . $this->user->getUserName() . '(' . $this->user->getId() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
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
