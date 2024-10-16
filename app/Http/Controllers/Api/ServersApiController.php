<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Xui\XuiConnect;
use App\Http\Resources\VpnuserResource;
use App\Models\Server;
use App\Models\Vpnuser;
use Illuminate\Http\Request;
use Curl\Curl;

class ServersApiController extends \App\Http\Controllers\Controller
{
    private array $xui = [];
    private array $amnezia = [];

    public function __construct()
    {
        $servers = Server::all()->where('active', 1);
        foreach ($servers as $server) {
            $serverAddress = "http://{$server->ip}/";
            $tunnelServerAddress = null;
            $username = $server->login;
            $password = $server->password;
            $panel = 1; # Panel Type x-ui (0) / 3x-ui (1)

            if ($server->type === 'xui') {
                $xui = new XuiConnect($serverAddress, $tunnelServerAddress, $username, $password, $panel);
                $xui->setDefaultProtocol('vless');
                $xui->setDefaultHeader('google.com');
                $xui->setDefaultTransmission('tcp');
                $xui->setSniffing(true, ['http', 'tls', 'quic']);
                $this->xui[] = $xui;
            }

            if ($server->type === 'amnezia') {
                $this->amnezia[] = $server;
            }
        }
    }

    public function index()
    {
        return Vpnuser::all();
    }

    public function show(Vpnuser $vpnuser)
    {
        return new VpnuserResource($vpnuser);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'tg_id' => 'required|max:255',
        ]);

        $name = $request->input('name');
        $tgId = $request->input('tg_id');
        $limitIp = $request->input('limit_ip', 0);

        $vpnuser = Vpnuser::updateOrCreate([
            'name' => $name,
            'tg_id' => $tgId,
        ]);

        /** @var \App\Http\Controllers\Xui\XuiConnect $xui */
        foreach ($this->xui as $xui) {
            $protocol = 'vless'; # vmess / vless / trojan
            $transmission = 'tcp'; # tcp / ws
            $xui->add($tgId, $tgId, 0, 0, $protocol, $transmission);

            $update = [
                'limitIp' => $limitIp, # Just for 3x-ui (1)
            ];
            $where = [
                'email' => $tgId,
            ];
            $xui->update($update, $where);
        }

        foreach ($this->amnezia as $amnezia) {
            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('password', $amnezia->password);
            $curl->post("http://{$amnezia->ip}/client", [
                'name' => $tgId,
            ]);
        }

        return response()->json([
            'data' => new VpnuserResource($vpnuser)
        ], 200);
    }

    public function update(Request $request, Vpnuser $vpnuser)
    {
        $tgId = $vpnuser->tg_id ?? $request->input('tg_id');

        if (empty($tgId)) {
            return response()->json([
            ], 404);
        }

        $update = [];

        if ($request->request->get('name')) {
            $update['name'] = $request->request->get('name');
        }

        $vpnuser->update($update);

        $update = [];
        if ($request->request->get('limit_ip')) {
            $update['limitIp'] = $request->request->get('limit_ip');
        }
        if ($request->request->get('expiry_time')
            || $request->request->get('expiry_time') === '0') {
            $time = $request->request->get('expiry_time');
            if ($time === 'now') {
                $time = time();
            }
            $update['expiryTime'] = $time;
        }
        if ($request->request->get('enable') === '0') {
            $update['enable'] = false;
        }
        if ($request->request->get('enable') === '1') {
            $update['enable'] = true;
        }

        $update['reset'] = 0;

        /** @var \App\Http\Controllers\Xui\XuiConnect $xui */
        foreach ($this->xui as $xui) {
            $where = [
                'email' => $tgId,
            ];
            $xui->update($update, $where);
        }

        foreach ($this->amnezia as $amnezia) {
            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('password', $amnezia->password);
            $users = $curl->get("http://{$amnezia->ip}/client");
            foreach ($users as $user) {
                if ($user->name == $tgId) {
                    $curl = new Curl();
                    $curl->setHeader('Content-Type', 'application/json');
                    $curl->setHeader('password', $amnezia->password);
                    if ($request->request->get('enable') === '1') {
                        $curl->post("http://{$amnezia->ip}/client/{$user->id}/enable");
                    } else if ($request->request->get('enable') === '0') {
                        $curl->post("http://{$amnezia->ip}/client/{$user->id}/disable");
                    }
                }
            }
        }

        return response()->json([
            'data' => new VpnuserResource($vpnuser)
        ], 200);
    }

    public function destroy(Vpnuser $vpnuser)
    {
        /** @var \App\Http\Controllers\Xui\XuiConnect $xui */
        foreach ($this->xui as $xui) {
            $where = [
                'email' => $vpnuser->tg_id,
            ];
            $xui->delete($where);
        }

        $vpnuser->delete();

        foreach ($this->amnezia as $amnezia) {
            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('password', $amnezia->password);
            $users = $curl->get("http://{$amnezia->ip}/client");
            foreach ($users as $user) {
                if ($user->name == $vpnuser->tg_id) {
                    $curl->delete("http://{$amnezia->ip}/client/$user->id");
                }
            }
        }

        return response()->json(null, 204);
    }

    public function getLink(Request $request, string $tgId)
    {
        /** @var \App\Http\Controllers\Xui\XuiConnect $xui */
        $xui = $this->xui[0];
        $where = [
            'email' => $tgId,
        ];

        $link = "";
        $qrCode = "";
        $response = $xui->fetch($where, env('VPN_DOMAIN_FOR_LINKS'));
        try {
            if (!$response["success"]) {
                return response()->json([
                ], 404);
            }

            $link = $response["obj"]["user"]["url"];
            $qrCode = [
                "html" => $response["obj"]["user"]["qrcode"]["html"],
                "svg" => $response["obj"]["user"]["qrcode"]["svg"]
            ];

            if (empty($link)) {
                return response()->json([
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json([
            ], 404);
        }

        return response()->json([
            'data' => [
                'link' => $link,
                'qr_code' => $qrCode,
            ],
        ], 200);
    }

    public function getAmneziaFile(Request $request, string $tgId)
    {
        $amnezia = $this->amnezia[0];
        if (empty($amnezia)) {
            return response()->json([
            ], 404);
        }

        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->setHeader('password', $amnezia->password);
        $users = $curl->get("http://{$amnezia->ip}/client");

        foreach ($users as $user) {
            if ($user->name == $tgId) {
                $curl = new Curl();
                $curl->setHeader('Content-Type', 'application/json');
                $curl->setHeader('password', $amnezia->password);
                $config = $curl->get("http://{$amnezia->ip}/client/{$user->id}/configuration");
                if (empty($config)) {
                    return response()->json([
                    ], 404);
                }

                return response()->make($config, '200', array(
                    'Content-Type' => 'application/octet-stream',
                    'Content-Disposition' => "attachment; filename='{$tgId}.config'"
                ));
            }
        }

        return response()->json([
        ], 404);
    }
}
