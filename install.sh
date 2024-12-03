function install() {
    apt-get upgrade -y
    apt-get update -y
    apt-get install sudo -y

    sudo apt-get install vim -y
    sudo apt-get install php -y
    sudo apt install php-xml -y
    sudo apt install php-dom -y
    sudo apt install php-sqlite3 -y
    sudo apt install php-curl -y
    sudo apt install composer -y
    sudo apt install npm -y
    sudo apt-get install sqlite3 -y

    cd database
    touch database.sqlite
    cd ../
    composer update
    npm install
    npm run build
    cp .env.example .env
    php artisan:migrate
    php artisan key:generate

    sudo systemctl stop apache2
    sudo systemctl disable apache2
    
    sudo apt -y install ca-certificates curl gnupg lsb-release
    curl -fsSL https://download.docker.com/linux/debian/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
    echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/debian $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
    sudo apt -y install docker-ce docker-ce-cli containerd.io
    sudo systemctl start docker
    sudo systemctl enable docker
    
    php artisan sail:install
    chmod -R guo+w storage
    php artisan cache:clear
    php artisan migrate
    ./vendor/bin/sail up -d

    sudo apt-get install supervisor
    touch /etc/supervisor/conf.d/laravel-worker.conf
    mkdir /var/www/ducks-server-manager/logs/
    touch /var/www/ducks-server-manager/logs/worker.log
    echo "[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/ducks-server-manager/artisan queue:work --timeout=180
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
;user=forge
numprocs=8
redirect_stderr=true
stdout_logfile=/var/www/ducks-server-manager/logs/worker.log
stopwaitsecs=3600" >> /etc/supervisor/conf.d/laravel-worker.conf
    sudo systemctl enable supervisor --now
    sudo supervisorctl reread
    sudo supervisorctl update
    sudo supervisorctl start "laravel-worker:*"

    clear
    echo "Installed DUCKS SERVERS MANAGER"
    echo "Congratulations! Now you must configure the .env (copy .env.example to .env) configuration file at .env"
}

install
