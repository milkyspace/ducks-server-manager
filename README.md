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

```sh
# DEBIAN 12
apt-get update
apt-get install git -y
mkdir /var/www
cd /var/www
git clone https://github.com/milkyspace/ducks-server-manager.git
cd ducks-server-manager
chmod u+x install.sh
./install.sh
# answer the installation questions if necessary

# if you run any Docker command from a regular user, the following error will be displayed in the terminal:
# got permission denied while trying to connect to the Docker daemon socket at unix:///var/run/docker.sock: Get "http://%2Fvar%2Frun%2Fdocker.sock/v1.24/containers/json": dial unix /var/run/docker.sock: connect: permission denied
sudo groupadd docker
sudo usermod -aG docker $USER

```

Change it .env: Enter your data using the example
```sh
nano .env # or vim .env
```
**APP_URL** is url of your app (example ip of the server http://192.168.0.1)
**VPN_DOMAIN_FOR_LINKS** is common domain for vpn servers
**VPN_DOMAIN_FOR_LINKS_{keyType}** is common domain for keytype vpn servers (example tiktok)

Change docker-compose.yml:
- Delete everything related to volume mysql
- sudo ./vendor/bin/sail down
- sudo ./vendor/bin/sail up -d

Set permissions:
- sudo chmod -R 777 ./storage
- sudo chmod -R 777 ./database
- sudo chmod -R 777 ./app/Http/Controllers/Inner/Xui

```sh
# settings for performing updates on servers
php artisan queue:failed # show failed tasks
php artisan queue:retry all # restart failed tasks

nano /etc/supervisor/conf.d/laravel-worker.conf # to change supervisor`s conf
sudo supervisorctl reread # reread supervisor`s conf
sudo supervisorctl update # update supervisor !important after code changing (if code is important for task)
sudo supervisorctl stop "laravel-worker:*" # stop workers
sudo supervisorctl start "laravel-worker:*" # start workers

crontab -e
# insert */20 * * * * cd /var/www/ducks-server-manager/ && php artisan queue:retry all >> /dev/null 2>&1
systemctl enable cron
```

Now you can open DSM at http://{$IP}/register to register at DSM

Postman collection for 3x-ui API https://documenter.getpostman.com/view/16802678/2s9YkgD5jm

## License

MIT License

Copyright (c) 2024 milkyspace

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

**Free Software**
