<?php
/**
 * File contains Class TransactionQueue
 *
 * @since 09.08.2018
 */

namespace UserBalanceApp\Queue;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class TransactionQueue
 *
 * @package UserBalanceApp\Queue
 */
class TransactionQueue implements QueueInterface
{
    const QUEUE_NAME = 'transaction_queue';

    /**
     * @var AMQPStreamConnection
     */
    protected $connection;

    /**
     * TransactionQueue constructor.
     *
     * @param AMQPStreamConnection $connection
     */
    public function __construct(AMQPStreamConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param array $payload
     *
     * @return void
     */
    public function push(array $payload)
    {
        $channel = $this->connection->channel();
        $channel->queue_declare(self::QUEUE_NAME, false, true, false, false);

        $message = new AMQPMessage(json_encode($payload));
        $channel->basic_publish($message, '', self::QUEUE_NAME);

        $channel->close();
        $this->connection->close();
    }
}
