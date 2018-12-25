#!/usr/bin/env bash

cd
mkdir Downloads

# Installation de Xdebug
cd ./Downloads
sudo wget https://xdebug.org/files/xdebug-2.6.1.tgz
tar -xvzf xdebug-2.6.1.tgz
cd xdebug-2.6.1/
phpize
./configure
make

sudo cp modules/xdebug.so /usr/lib/php/20151012
echo 'zend_extension=/usr/lib/php/20151012/xdebug.so
xdebug.remote_enable=1
xdebug.remote_autostart=1
xdebug.remote_connect_back=1
xdebug.default_enable=1
xdebug.remote_host=10.0.2.2
xdebug.remote_port=9000
xdebug.idekey=PHPSTORM' | sudo tee /etc/php/7.2/mods-available/xdebug.ini

sudo ln -sf /etc/php/7.2/mods-available/xdebug.ini /etc/php/7.2/fpm/conf.d/20-xdebug.ini
sudo ln -sf /etc/php/7.2/mods-available/xdebug.ini /etc/php/7.2/cli/conf.d/20-xdebug.ini
sudo service php7.2-fpm restart

# Préparation
cd ~/code
php artisan key:generate
php artisan migrate
php artisan passport:install
php artisan lan:permissions

# Création de la deuxième base de donnée
sudo mysql -u homestead "-psecret" -e "create database lanadepttest"

cd
rm -rf ~/Downloads
