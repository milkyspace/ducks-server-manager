<?php

namespace App\Http\Controllers\Business\Servers;

interface IServerController
{
    public function addUser(User $user): void;

    public function updateUser(User $user): void;

    public function destroyUser(User $user): void;

    public function getLink(User $user): ?string;

    public function getFile(User $user): ?string;
}
