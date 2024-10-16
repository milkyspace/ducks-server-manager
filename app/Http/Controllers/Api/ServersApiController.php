<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Xui\XuiConnect;
use App\Http\Resources\VpnuserResource;
use App\Models\Server;
use App\Models\Vpnuser;
use Illuminate\Http\Request;

class ServersApiController extends \App\Http\Controllers\Controller
{
    private array $xui = [];

    public function __construct()
    {
        $servers = Server::all()->where('active', 1);
        foreach ($servers as $server) {
            $serverAddress = "http://{$server->ip}/";
            $tunnelServerAddress = null;
            $username = $server->login;
            $password = $server->password;
            $panel = 1; # Panel Type x-ui (0) / 3x-ui (1)

            $xui = new XuiConnect($serverAddress, $tunnelServerAddress, $username, $password, $panel);
            $xui->setDefaultProtocol('vless');
            $xui->setDefaultHeader('google.com');
            $xui->setDefaultTransmission('tcp');
            $xui->setSniffing(true, ['http', 'tls', 'quic']);

            $this->xui[] = $xui;
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

        return response()->json([
            'data' => new VpnuserResource($vpnuser)
        ], 200);
    }

    public function update(Request $request, Vpnuser $vpnuser)
    {
        $tgId = $vpnuser->tg_id;

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
        $response = $xui->fetch($where, "ducks.tel");
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
}
