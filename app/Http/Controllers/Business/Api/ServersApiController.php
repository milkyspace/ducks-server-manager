<?php

namespace App\Http\Controllers\Business\Api;

use App\Http\Controllers\Business\Servers\AmneziaServerController;
use App\Http\Controllers\Business\Servers\IServerController;
use App\Http\Controllers\Business\Servers\User;
use App\Http\Controllers\Business\Servers\XuiServerController;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessAddingUser;
use App\Jobs\ProcessUpdatingUser;
use App\Models\Server;
use App\Models\Vpnuser;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ServersApiController extends Controller
{
    private Collection $servers;
    private Collection $serversSimple;

    public function __construct()
    {
        $serversData = Server::all()->where('active', 1);
        $servers = [];
        $serversSimple = [];
        foreach ($serversData as $serverData) {
            $server = (new \App\Http\Controllers\Business\Servers\Server())
                ->setType($serverData->type)
                ->setAddress($serverData->ip)
                ->setUser($serverData->login)
                ->setPassword($serverData->password);
            $serversSimple[] = $server;

            foreach ([XuiServerController::class, AmneziaServerController::class] as $controller) {
                /** @var IServerController $controller */
                if ($controller::TYPE === $serverData->type) {
                    $servers[] = new $controller($server);
                }
            }
        }

        $this->servers = collect($servers);
        $this->serversSimple = collect($serversSimple);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|max:255',
        ]);

        $id = $request->input('id');
        $name = $request->input('name');
        $limitIp = $request->input('limit_ip', 3);
        $type = $request->input('type', '');

        $user = (new User())
            ->setId($id)
            ->setType($type)
            ->setUserName($name)
            ->setEnable(true)
            ->setLimitIp($limitIp)
            ->setExpiryTime(0);

        /** @var IServerController $server */
//        foreach ($this->servers->all() as $server) {
//            $server->addUser($user);
//        }

        foreach ($this->serversSimple->all() as $server) {
            ProcessAddingUser::dispatch($server, $user);
        }

        return response()->json();
    }

    public function update(Request $request, $id)
    {
        if ($id === 0) {
            $users = $request->input('users');

            foreach ($users as $userId) {
                $user = (new User())->setId($userId);
                $user->setType('xui');

                $enable = $request->input('enable');
                if ($enable === '0') {
                    $user->setEnable(false);
                } elseif ($enable === '1') {
                    $user->setLimitIp(3);
                    $user->setEnable(true);
                }

                foreach ($this->serversSimple->all() as $server) {
                    ProcessUpdatingUser::dispatch($server, $user);
                }

                return response()->json();
            }
        }

        if (empty($id)) {
            return response()->json([], 404);
        }

        $user = (new User())->setId($id);

        $limitIp = $request->input('limit_ip');
        if (!empty($limitIp) || $limitIp === 0) {
            $user->setLimitIp($limitIp);
        }

        $expiryTime = $request->input('expiry_time');
        if ($expiryTime || $expiryTime === '0') {
            $time = $expiryTime;
            if ($time === 'now') {
                $time = time();
            }
            $user->setExpiryTime($time);
        }

        $enable = $request->input('enable');
        if ($enable === '0') {
            $user->setEnable(false);
        } elseif ($enable === '1') {
            $user->setEnable(true);
        }

        /** @var IServerController $server */
        foreach ($this->servers->all() as $server) {
//            $server->updateUser($user);
        }

        foreach ($this->serversSimple->all() as $server) {
            ProcessUpdatingUser::dispatch($server, $user);
        }

        return response()->json();
    }

    public function destroy(Vpnuser $vpnuser)
    {
        $user = (new User())->setId($vpnuser->tg_id);
        /** @var IServerController $server */
        foreach ($this->servers->all() as $server) {
            $server->destroyUser($user);
        }

        return response()->json(null, 204);
    }

    public function getLink(Request $request, string $id, ?string $keyType = 'default')
    {
        $user = (new User())->setId($id);
        $link = '';
        /** @var IServerController $server */
        foreach ($this->servers->all() as $server) {
            $link = $server->getLink($user, $keyType);
            if (!empty($link)) {
                break;
            }
        }

        return response()->json([
            'data' => [
                'link' => $link,
                'qr_code' => '',
            ],
        ]);
    }

    public function getFile(Request $request, string $id)
    {
        $file = '';
        $user = (new User())->setId($id);
        /** @var IServerController $server */
        foreach ($this->servers->all() as $server) {
            $file = $server->getFile($user);
            if (!empty($file)) {
                break;
            }
        }

        return response()->make($file, '200', array(
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => "attachment; filename='{$id}.config'"
        ));
    }

    public function getUsersList(Request $request)
    {
        $usersByServers = [];
        /** @var IServerController $server */
        foreach ($this->servers->all() as $server) {
            $usersByServers[$server->getServer()->getAddress()] = $server->getUsersList();
        }

        return response()->json([
            'users_by_servers' => $usersByServers,
        ]);
    }

    public function getUser(Request $request, string $id)
    {
        $user = (new User())->setId($id);

        $userByServers = [];
        /** @var IServerController $server */
        foreach ($this->servers->all() as $server) {
            $userByServers[$server->getServer()->getAddress()] = $server->getUser($user);
        }

        return response()->json([
            'user_by_servers' => $userByServers,
        ]);
    }
}
