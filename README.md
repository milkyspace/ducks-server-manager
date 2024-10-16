# 🦆 DUCKS SERVER MANAGER (DSM)
## _Make managing vpn servers simple_

[![Telegram](https://img.shields.io/badge/Telegram-2CA5E0?style=for-the-badge&logo=telegram&logoColor=white)](https://t.me/vpnducks_support)

DSM is an application for managing servers used for VPN.
dvpn allows you to deliver the same information about users and subscriptions to each of the servers, and collect information about the status of the servers

## Tech

DSM uses technical solutions:
- Laravel
- Sail by Laravel

And of course DUCKS VPN itself is open source with a public repository on GitHub.

## Installation

DUCKS VPN requires PHP to run.\

```sh
git clone https://github.com/milkyspace/ducks-server-manager.git
cd ducks-server-manager
chmod u+x install.sh
./install.sh

# answer the installation questions if necessary
```

Change it .env: Enter your data using the example
```sh
nano .env # or vim .env
```

**APP_URL** is url of your app (example ip of the server http://192.168.0.1)

Now you can open DSM at http://{$IP}/register to register at DSM


## License

MIT

**Free Software**
