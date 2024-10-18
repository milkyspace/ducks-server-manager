<?php

namespace App\Http\Controllers\Business\Servers;

use Curl\Curl;

class AmneziaServerController
{
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

    public function addUser(User $user): void
    {
        $amneziaUserId = $this->getAmneziaUserId($user);
        if (empty($amneziaUserId)) {
            $this->amneziaConnect->post("http://{$this->server->getAddress()}/client", ['name' => $user->getId(),]);
        }
    }

    public function updateUser(User $user): void
    {
        $amneziaUserId = $this->getAmneziaUserId($user);
        if (empty($amneziaUserId)) {
            if ($user->isEnable() === true) {
                $this->addUser($user);
            }
            return;
        }

        if ($user->isEnable() === true) {
            $this->amneziaConnect->post("http://{$this->server->getAddress()}/client/{$amneziaUserId}/enable");
        } else if ($user->isEnable() === false) {
            $this->amneziaConnect->post("http://{$this->server->getAddress()}/client/{$amneziaUserId}/disable");
        }
    }

    public function destroyUser(User $user): void
    {
        $amneziaUserId = $this->getAmneziaUserId($user);
        if (empty($amneziaUserId)) {
            return;
        }

        $this->amneziaConnect->delete("http://{$this->server->getAddress()}/client/$amneziaUserId");
    }

    public function getLink(User $user): ?string
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
}
