User balance service
------------

# Description
This is simple implementation of microservice responsible for processing transactions on users balance.

Technologies used:
1. Docker to build environment
2. PHP 7.2
3. Rabbitmq as queue broker
4. MySQL for storing user balance and transactions data

Frameworks and libraries:
1. php-amqplib/php-amqplib - Rabbitmq php client library
2. symfony/console - to build cli application
3. php-di/php-di - for dependency injection management
4. mnapoli/silly - cli microframework
5. symfony/event-dispatcher - for events dispatching
6. psr/log - to inject logger dependency

# Installation

1. Clone the repository
2. Run `docker-compose up -d` inside project directory

# Test the service

To run worker 
1. Enter the app container `docker-compose run app bash`
2. Run worker `php bin/index.php app:run-transaction-worker`

To push jobs to queue
1. Enter the app container `docker-compose run app bash`
2. To credit user balance `php bin/index.php transaction:push:credit <transaction_id> <user> <amount>`
3. To withdraw user balance `php bin/index.php  transaction:push:withdraw <transaction_id> <user> <amount>`
4. To transfer `php bin/index.php  transaction:push:transfer <transaction_id> <user> <amount>`

