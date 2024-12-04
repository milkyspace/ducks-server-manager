<?php

namespace App\Jobs;

use App\Http\Controllers\Business\Servers\IServerController;
use App\Http\Controllers\Business\Servers\User;
use App\Http\Controllers\Business\Servers\XuiServerController;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class ProcessUpdatingUser implements ShouldQueue
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
                    $isUpdate = $server->updateUser($this->user);
                    if (array_key_exists('add_user', $isUpdate) && $isUpdate['add_user'] === true) {
                        ProcessAddingUser::dispatch($this->server, $this->user);
                    }
                    if ($isUpdate['success'] !== true) {
                        sleep(2);
                        $this->fail('Не обновился пользователь ' . $this->user->getUserName() . '(' . $this->user->getId() . ') на сервере ' . $server->getServer()->getAddress() . json_encode($isUpdate));
                    }
                }
            }
        } catch (\Exception $e) {
            sleep(2);
            $this->fail('Exception Не обновился пользователь ' . $this->user->getUserName() . '(' . $this->user->getId() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
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
