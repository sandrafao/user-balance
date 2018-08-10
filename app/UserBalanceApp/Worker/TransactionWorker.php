<?php
/**
 * File contains Class TransactionWorker
 *
 * @since 08.08.2018
 */

namespace UserBalanceApp\Worker;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use UserBalanceApp\Balance\Exception\RetryTransaction;
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
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * TransactionWorker constructor.
     *
     * @param AMQPStreamConnection $connection
     * @param TransactionService   $service
     * @param LoggerInterface      $logger
     */
    public function __construct(
        AMQPStreamConnection $connection,
        TransactionService $service,
        LoggerInterface $logger
    ) {
        $this->connection = $connection;
        $this->service    = $service;
        $this->logger     = $logger;
    }

    public function run()
    {
        try {
            $this->runWorker();
        } catch (\Throwable $exception) {
            $this->logger->critical('Worker failed execution failed', ['exception' => $exception]);
            throw $exception;
        }
    }

    /**
     * @param AMQPMessage $message
     */
    public function processMessage(AMQPMessage $message)
    {
        /** @var AMQPChannel $chanel */
        $chanel = $message->delivery_info['channel'];
        try {
            $this->service->runTransaction(json_decode($message->body, true));
        } catch (RetryTransaction $exception) {
            $chanel->basic_reject($message->delivery_info['delivery_tag'], true);
            $this->logger->error('Transaction failed and requeued', ['exception' => $exception]);
            echo " Error. Requeued. {$exception->getMessage()}\n";
            return;
        } catch (\Throwable $exception) {
            $chanel->basic_reject($message->delivery_info['delivery_tag'], false);
            $this->logger->error('Failed to process message', ['exception' => $exception]);
            echo " Error. Not requeued. {$exception->getMessage()}\n";
            return;
        }
        echo " Message processed successfully\n";
        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
    }

    protected function runWorker()
    {
        $channel = $this->connection->channel();

        $channel->basic_qos(null, 1, null);
        $channel->queue_declare(TransactionQueue::QUEUE_NAME, false, true, false, false);

        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        $channel->basic_consume(
            TransactionQueue::QUEUE_NAME,
            '', false, false, false, false,
            [$this, 'processMessage']
        );

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $this->connection->close();
    }
}
