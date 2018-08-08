<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Psr\Container\ContainerInterface;
use UserBalanceApp\Balance\Driver\TransactionDriver;
use UserBalanceApp\Balance\Driver\TransactionDriverInterface;
use UserBalanceApp\Queue\QueueInterface;
use UserBalanceApp\Queue\TransactionQueue;

return [
    AMQPStreamConnection::class => function() {
        return new AMQPStreamConnection(
            getenv('RABBITMQ_HOST'),
            getenv('RABBITMQ_PORT'),
            getenv('RABBITMQ_DEFAULT_USER'),
            getenv('RABBITMQ_DEFAULT_PASS')
        );
    },
    TransactionDriverInterface::class => function() {
        $dsn = sprintf(
            'mysql:dbname=%s;host=%s;port=%s;charset=utf8',
            getenv('MYSQL_DATABASE'),
            getenv('MYSQL_HOST'),
            getenv('MYSQL_PORT')
        );
        $connection  = new \PDO($dsn, getenv('MYSQL_USER'), getenv('MYSQL_PASSWORD'));
        return new TransactionDriver($connection);
    },
    QueueInterface::class => function(ContainerInterface $container) {
        return new TransactionQueue($container->get(AMQPStreamConnection::class));
    },
];