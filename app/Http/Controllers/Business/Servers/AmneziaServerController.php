<?php

namespace App\Http\Controllers\Business\Servers;

use Curl\Curl;

class AmneziaServerController implements IServerController
{
    const TYPE = 'amnezia';

    private \Curl\Curl $amneziaConnect;
    private Server $server;

    public function __construct(Server $server)
    {
        $this->server = $server;

        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->setHeader('password', $server->getPassword());

        $this->amneziaConnect = $curl;
    }

    /**
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * @param Server $server
     */
    public function setServer(Server $server)
    {
        $this->server = $server;
        return $this;
    }

    private function getAmneziaUserId(User $user): ?string
    {
        $amneziaUsers = $this->amneziaConnect->get("http://{$this->server->getAddress()}/client");
        foreach ($amneziaUsers as $amneziaUser) {
            if ($amneziaUser->name == $user->getId()) {
                return $amneziaUser->id;
            }
        }

        return null;
    }

    public function addUser(User $user, ?array $data = []): array
    {
        // Создаем конфиг только тогда, когда сменился протокол на Amnezia
        if (empty($user->getType())) {
            return false;
        }

        // Если пользователь уходит с протокола Amnezia, удаляем конфигурацию, чтобы освободить ip
        if ($user->getType() !== static::TYPE) {
            $amneziaUserId = $this->getAmneziaUserId($user);
            if (!empty($amneziaUserId)) {
                $this->amneziaConnect->delete("http://{$this->server->getAddress()}/client/$amneziaUserId");
            }
            return false;
        }

        $amneziaUserId = $this->getAmneziaUserId($user);
        if (empty($amneziaUserId)) {
            $this->amneziaConnect->post("http://{$this->server->getAddress()}/client", ['name' => $user->getId(),]);
        }

        return true;
    }

    public function updateUser(User $user): array
    {
        $amneziaUserId = $this->getAmneziaUserId($user);
        if (empty($amneziaUserId)) {
            if ($user->isEnable() === true) {
                $this->addUser($user);
            }
            return [];
        }

        if ($user->isEnable() === true) {
            $this->amneziaConnect->post("http://{$this->server->getAddress()}/client/{$amneziaUserId}/enable");
        } else if ($user->isEnable() === false) {
            $this->amneziaConnect->post("http://{$this->server->getAddress()}/client/{$amneziaUserId}/disable");
        }
        return [];
    }

    public function destroyUser(User $user): void
    {
        $amneziaUserId = $this->getAmneziaUserId($user);
        if (empty($amneziaUserId)) {
            return;
        }

        $this->amneziaConnect->delete("http://{$this->server->getAddress()}/client/$amneziaUserId");
    }

    public function getLink(User $user, ?string $keyType = 'default'): ?string
    {
        return null;
    }

    public function getFile(User $user): ?string
    {
        $amneziaUserId = $this->getAmneziaUserId($user);
        if (empty($amneziaUserId)) {
            return null;
        }

        $config = $this->amneziaConnect->get("http://{$this->server->getAddress()}/client/{$amneziaUserId}/configuration");
        return $config ?? null;
    }

    public function getUsersList(): ?array
    {
        return [];
    }
}
