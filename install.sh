function install() {
    apt-get install sudo

    sudo apt-get install vim -y
    sudo apt-get install php -y
    sudo apt install php-xml -y
    sudo apt install php-dom -y
    sudo apt install php-sqlite3 -y
    sudo apt install php-curl -y
    sudo apt install composer -y
    sudo apt install npm -y
    sudo apt-get install sqlite3 -y

    cd storage
    sqlite3 database.sqlite < schema_db.sql
    cd ../
    composer update
    npm install
    npm run build
    cp .env.example .env
    php artisan:migrate
    php artisan key:generate

    sudo systemctl stop apache2
    php artisan sail:install
    chmod -R guo+w storage
    php artisan cache:clear
    ./vendor/bin/sail up -d

    clear
    echo "Installed DUCKS SERVERS MANAGER"
    echo "Congratulations! Now you must configure the .env (copy .env.example to .env) configuration file at .env"
}

install
