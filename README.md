# psyhealth-api

## setup

composer install

copy paste the .env to .env.local and change the database credentials

php bin/console d:d:c

php bin/console d:m:m

php bin/console d:f:l

## generate lexik jwt keys

php bin/console lexik:jwt:generate-keypair


# launch the server

php bin/console serve

php -S localhost:8000 -t public

