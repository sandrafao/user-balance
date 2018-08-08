<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use UserBalanceApp\Balance\Driver\TransactionDriver;

return [
    AMQPStreamConnection::class => function() {
        return new AMQPStreamConnection(
            getenv('RABBITMQ_HOST'),
            getenv('RABBITMQ_PORT'),
            getenv('RABBITMQ_DEFAULT_USER'),
            getenv('RABBITMQ_DEFAULT_PASS')
        );
    },
    TransactionDriver::class => function() {
        $dsn = sprintf(
            'mysql:dbname=%s;host=%s;port=%s;charset=utf8',
            getenv('MYSQL_DATABASE'),
            getenv('MYSQL_HOST'),
            getenv('MYSQL_PORT')
        );
        $connection  = new \PDO($dsn, getenv('MYSQL_USER'), getenv('MYSQL_PASSWORD'));
        return new TransactionDriver($connection);
    },
];