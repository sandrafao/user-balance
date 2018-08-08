<?php
/**
 * File contains Class TransactionWorker
 *
 * @since 08.08.2018
 */

namespace UserBalanceApp\Worker;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use UserBalanceApp\Balance\Service\TransactionService;
use UserBalanceApp\Queue\TransactionQueue;

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
     * @var TransactionService
     */
    protected $service;

    /**
     * TransactionWorker constructor.
     *
     * @param AMQPStreamConnection $connection
     * @param TransactionService   $service
     */
    public function __construct(AMQPStreamConnection $connection, TransactionService $service)
    {
        $this->connection = $connection;
        $this->service    = $service;
    }

    public function run()
    {
        $channel = $this->connection->channel();

        $channel->queue_declare(TransactionQueue::QUEUE_NAME, false, false, false, false);

        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        $callback = function ($message) {
            $this->service->runTransaction(json_decode($message->body, true));
        };

        $channel->basic_consume(TransactionQueue::QUEUE_NAME, '', false, true, false, false, $callback);

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $this->connection->close();
    }
}
