<?php
chdir(dirname(__DIR__));

include __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$dotenv = new Dotenv\Dotenv(__DIR__ . '/../');
$dotenv->load();

$connection = new AMQPStreamConnection('rabbitmq', 5672, getenv('RABBITMQ_DEFAULT_USER'), 'N@ZFVG8JB+gH$w+6');
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false);

$msg = new AMQPMessage('Hello World!');
$channel->basic_publish($msg, '', 'hello');

echo " [x] Sent 'Hello World!'\n";

$channel->close();
$connection->close();