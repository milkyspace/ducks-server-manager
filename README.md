# 🦆 DUCKS SERVER MANAGER (DSM)
## _Make managing vpn servers simple_

[![License: MIT](https://img.shields.io/badge/License-MIT-lightblue.svg)](https://opensource.org/licenses/MIT)

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

#If you run any Docker command from a regular user, the following error will be displayed in the terminal:
#Got permission denied while trying to connect to the Docker daemon socket at unix:///var/run/docker.sock: Get "http://%2Fvar%2Frun%2Fdocker.sock/v1.24/containers/json": dial unix #/var/run/docker.sock: connect: permission denied
sudo groupadd docker
sudo usermod -aG docker $USER

```

Change it .env: Enter your data using the example
```sh
nano .env # or vim .env
```

Change docker-compose.yml:
- Delete everything related to volume mysql
- sudo ./vendor/bin/sail down
- sudo ./vendor/bin/sail up -d


**APP_URL** is url of your app (example ip of the server http://192.168.0.1)

Now you can open DSM at http://{$IP}/register to register at DSM


## License

MIT

**Free Software**
