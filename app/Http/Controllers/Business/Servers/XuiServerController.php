<?php

namespace App\Http\Controllers\Business\Servers;

use App\Http\Controllers\Inner\Xui\XuiConnect;
use App\Jobs\ProcessUpdatingUser;

class XuiServerController implements IServerController
{
    const TYPE = 'xui';

    private Server $server;

    private \App\Http\Controllers\Inner\Xui\XuiConnect $xuiConnect;

    public function __construct(Server $server)
    {
        $this->server = $server;

        $xui = new XuiConnect(
            "http://{$server->getAddress()}/",
            null,
            $server->getUser(),
            $server->getPassword(),
            1
        );
        $xui->setDefaultProtocol('vless');
        $xui->setDefaultHeader('google.com');
        $xui->setDefaultTransmission('tcp');
        $xui->setSniffing(true, ['http', 'tls', 'quic']);

        $this->xuiConnect = $xui;
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

    public function addUser(User $user, ?array $data = []): array
    {
        try {
            $response = $this->xuiConnect->fetch(['email' => $user->getId(),]);
            if ($response['success'] === true && $response['msg'] === 'User found successfully') {
                return [
                    'success' => true,
                ];
            }
            $isAdded = $this->xuiConnect->add($user->getId(), $user->getId(), 0, 0, $this->server->getDefaultProtocol(), $this->server->getDefaultTransmission());
            if ($isAdded['success'] === true) {
                ProcessUpdatingUser::dispatch($this->server, $user);
                return [
                    'success' => true,
                ];
            } else if (str_contains($isAdded['msg'], 'Duplicate email')) {
                return [
                    'success' => true,
                    'result' => $isAdded,
                ];
            } else {
                return [
                    'success' => false,
                    'result' => $isAdded,
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'result' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ];
        }
    }

    public function updateUser(User $user): array
    {
        $update = [];
        if (!empty($user->getLimitIp()) || $user->getLimitIp() === 0) {
            $update['limitIp'] = $user->getLimitIp();
        }

        if (!empty($user->getExpiryTime())) {
            $time = $user->getExpiryTime();
            if ($time === 'now') {
                $time = time();
            }
            $update['expiryTime'] = $time;
        }

        if ($user->isEnable() === false) {
            $update['enable'] = false;
        } elseif ($user->isEnable() === true) {
            $update['enable'] = true;
            $update['expiryTime'] = 0;
        }

        $update['reset'] = 0;

        try {
            $isUpdate = $this->xuiConnect->update($update, ['email' => $user->getId(),]);
            if ($isUpdate['success'] === true && $isUpdate['msg'] === 'Update Successfully') {
                return [
                    'success' => true,
                    'result' => $isUpdate,
                ];
            } elseif (str_contains($isUpdate['msg'], 'Not results found')) {
                return [
                    'success' => false,
                    'add_user' => true,
                    'result' => $isUpdate,
                ];
            } else {
                return [
                    'success' => false,
                    'result' => $isUpdate,
                ];
            }
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'error' => $th->getMessage(),
            ];
        }
    }

    public function destroyUser(User $user): void
    {
        $this->xuiConnect->delete(['email' => $user->getId(),]);
    }

    public function getLink(User $user, ?string $keyType = 'default'): ?string
    {
        switch ($keyType) {
            case 'tiktok':
                $address = env('VPN_DOMAIN_FOR_LINKS_TIKTOK');
                break;
            case 'default':
            default:
                $address = env('VPN_DOMAIN_FOR_LINKS');
                break;
        }

        $keyType = str_replace(['iPhone', 'Android', 'Windows', 'MacOS[;'], '', $keyType);

        $link = "vless://{$user->getId()}@{$address}:443?type=tcp&security=reality&pbk=IarU4xicX8qiVO5nqzeHc6MK9A_Vw9YaAtnPHB4DswU&fp=chrome&sni=google.com&sid=409c9524370c&spx=%2F&flow=xtls-rprx-vision#VPNDUCKS{$keyType}";

//        $response = $this->xuiConnect->fetch(['email' => $user->getId(),], $address, $keyType);
//        if ($response['success'] !== true) {
//            return '';
//        }
//        return $response["obj"]["user"]["url"];

        return $link;
    }

    public function getFile(User $user): ?string
    {
        return null;
    }

    public function getUsersList(): ?array
    {
        $response = $this->xuiConnect->fetchAll([]);
        return $response['clients'] ?: [];
    }

    public function getUser(User $user): ?array
    {
        $response = $this->xuiConnect->fetch(['email' => $user->getId(),]);
        return $response ?: [];
    }
}
