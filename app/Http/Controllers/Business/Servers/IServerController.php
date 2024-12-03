<?php

namespace App\Http\Controllers\Business\Servers;

interface IServerController
{
    const TYPE = "";

    public function getServer(): Server;

    public function setServer(Server $server);

    public function addUser(User $user, ?array $data = []): array;

    public function updateUser(User $user): array;

    public function destroyUser(User $user): void;

    public function getLink(User $user, ?string $keyType = 'default'): ?string;

    public function getFile(User $user): ?string;

    public function getUsersList(): ?array;
}
