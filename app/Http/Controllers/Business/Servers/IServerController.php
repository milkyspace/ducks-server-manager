<?php

namespace App\Http\Controllers\Business\Servers;

interface IServerController
{
    const TYPE = "";

    public function addUser(User $user, ?array $data = []): void;

    public function updateUser(User $user): void;

    public function destroyUser(User $user): void;

    public function getLink(User $user): ?string;

    public function getFile(User $user): ?string;
}
