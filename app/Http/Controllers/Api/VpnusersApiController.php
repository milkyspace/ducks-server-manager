<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Xui\XuiConnect;
use App\Http\Resources\VpnuserResource;
use App\Models\Server;
use App\Models\Vpnuser;
use Illuminate\Http\Request;

class VpnusersApiController extends \App\Http\Controllers\Controller
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
        return response()->json([
            'data' => $request->toArray()
        ], 200);
        $request->validate([
            'name' => 'required',
            'tg_id' => 'required|unique:vpnusers|max:255',
        ]);

        $name = $request->input('name');
        $tgId = $request->input('tg_id');

        $limitIp = $request->input('limit_ip', 3);
        $daysToAdd = $request->input('days_to_add', 7);

        $vpnuser = Vpnuser::create([
            'name' => $name,
            'tg_id' => $tgId,
        ]);

        /** @var \App\Http\Controllers\Xui\XuiConnect $xui */
        foreach ($this->xui as $xui) {
            $expiryDays = $daysToAdd; # Days / Unlimited (0)
            $protocol = 'vless'; # vmess / vless / trojan
            $transmission = 'tcp'; # tcp / ws
            $xui->add($tgId, 0, $expiryDays, $protocol, $transmission);

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
        $name = $request->input('name');
        $tgId = $request->input('tg_id');

        $limitIp = $request->input('limit_ip');
        $expiryTime = $request->input('expiry_time');

        $vpnuser->update([
            'name' => $name,
            'tg_id' => $tgId,
        ]);

        /** @var \App\Http\Controllers\Xui\XuiConnect $xui */
        foreach ($this->xui as $xui) {
            $update = [
                'expiryTime' => $expiryTime, # time() + (60 * 60 * 24) * (10 /* Days */)
                'resetUsage' => true, # true/false,
                'limitIp' => $limitIp, # Just for 3x-ui (1)
                'enable' => true, # true/false
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
        $response = $xui->fetch($where);
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
