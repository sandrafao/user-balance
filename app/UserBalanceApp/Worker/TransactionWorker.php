<?php
/**
 * File contains Class TransactionWorker
 *
 * @since 08.08.2018
 */

namespace UserBalanceApp\Worker;

use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * Class TransactionWorker
 *
 * @package UserBalanceApp\Worker
 */
class TransactionWorker
{
    /**
     * @var AMQPStreamConnection
     */
    protected $connection;

    /**
     * TransactionWorker constructor.
     *
     * @param AMQPStreamConnection $connection
     */
    public function __construct(AMQPStreamConnection $connection)
    {
        $this->connection = $connection;
    }

    public function run()
    {
        $channel = $this->connection->channel();

        $channel->queue_declare('hello', false, false, false, false);

        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        $callback = function ($msg) {
            echo ' [x] Received ', $msg->body, "\n";
        };

        $channel->basic_consume('hello', '', false, true, false, false, $callback);

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $this->connection->close();
    }
}
