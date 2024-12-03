<?php

namespace App\Jobs;

use App\Http\Controllers\Business\Servers\IServerController;
use App\Http\Controllers\Business\Servers\User;
use App\Http\Controllers\Business\Servers\XuiServerController;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class ProcessAddingUser implements ShouldQueue
{
    use Queueable;

    public $timeout = 180;

    public $tries = 15;

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
                    if ($isAdded['success'] !== true) {
                        $this->fail('Не добавился пользователь ' . $this->user->getUserName() . '(' . $this->user->getId() . ') на сервере ' . $server->getServer()->getAddress() . ' ' . json_encode($isAdded));
                    }
                }
            }
        } catch (\Exception $e) {
            $this->fail('Exception Не добавился пользователь ' . $this->user->getUserName() . '(' . $this->user->getId() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
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

    /**
     * Обработать провал задания.
     */
    public function failed(?Throwable $exception): void
    {
        $this->release(5);
    }
}
