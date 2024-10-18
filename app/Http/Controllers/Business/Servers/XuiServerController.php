<?php

namespace App\Http\Controllers\Business\Servers;

use App\Http\Controllers\Inner\Xui\XuiConnect;

class XuiServerController
{
    private \App\Http\Controllers\Inner\Xui\XuiConnect $xuiConnect;

    public function __construct(Server $server)
    {
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

    public function addUser(User $user): void
    {
        /** @var Server $server */
        $this->xuiConnect->add($user->getId(), $user->getId(), 0, 0, $server->getDefaultProtocol(), $server->getDefaultTransmission());
        $this->updateUser($user);
    }

    public function updateUser(User $user): void
    {
        $update = [];
        if (!empty($user->getLimitIp()) || $user->getLimitIp() === 0) {
            $update['limitIp'] = $user->getLimitIp();
        }

        if (!empty($user->getExpiryTime()) || $user->getExpiryTime() === 0) {
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
        }

        $update['reset'] = 0;

        $this->xuiConnect->update($update, ['email' => $user->getId(),]);
    }

    public function destroyUser(User $user): void
    {
        $this->xuiConnect->delete(['email' => $user->getId(),]);
    }

    public function getLink(User $user): ?string
    {
        $response = $this->xuiConnect->fetch(['email' => $user->getId(),], env('VPN_DOMAIN_FOR_LINKS'));
        return $response["obj"]["user"]["url"];
    }

    public function getFile(User $user): ?string
    {
        return null;
    }
}
