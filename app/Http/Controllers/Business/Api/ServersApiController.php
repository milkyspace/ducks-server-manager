<?php

namespace App\Http\Controllers\Business\Api;

use App\Http\Controllers\Business\Servers\AmneziaServerController;
use App\Http\Controllers\Business\Servers\IServerController;
use App\Http\Controllers\Business\Servers\User;
use App\Http\Controllers\Business\Servers\XuiServerController;
use App\Models\Server;
use App\Models\Vpnuser;
use Illuminate\Http\Request;
use function Symfony\Component\String\u;

class ServersApiController extends \App\Http\Controllers\Controller
{
    private \Illuminate\Support\Collection $servers;

    public function __construct()
    {
        $serversData = Server::all()->where('active', 1);
        $servers = [];
        foreach ($serversData as $serverData) {
            $server = (new \App\Http\Controllers\Business\Servers\Server())
                ->setAddress($serverData->ip)
                ->setUser($serverData->login)
                ->setPassword($serverData->password);

            switch ($serverData->type) {
                case 'xui':
                    $servers[] = new XuiServerController($server);
                    break;
                case 'amnezia':
                    $servers[] = new AmneziaServerController($server);
                    break;
            }
        }

        $this->servers = collect($servers);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|max:255',
        ]);

        $id = $request->input('id');
        $limitIp = $request->input('limit_ip', 3);

        $user = (new User())
            ->setId($id)
            ->setEnable(true)
            ->setLimitIp($limitIp)
            ->setExpiryTime(0);

        /** @var IServerController $server */
        foreach ($this->servers->all() as $server) {
            $server->addUser($user);
        }

        return response()->json();
    }

    public function update(Request $request, $id)
    {
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
            $server->updateUser($user);
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

    public function getLink(Request $request, string $id)
    {
        $user = (new User())->setId($id);
        $link = '';
        /** @var IServerController $server */
        foreach ($this->servers->all() as $server) {
            $link = $server->getLink($user);
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
}
