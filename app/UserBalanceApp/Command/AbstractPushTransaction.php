<?php
/**
 * File contains Class AbstractPushTransaction
 *
 * @since 09.08.2018
 */

namespace UserBalanceApp\Command;

use Silly\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use UserBalanceApp\Queue\QueueInterface;

/**
 * Class AbstractPushTransaction
 *
 * @package UserBalanceApp\Command
 */
abstract class AbstractPushTransaction extends Command
{
    /**
     * @var QueueInterface
     */
    protected $queue;

    /**
     * PushTransactionCommand constructor.
     *
     * @param QueueInterface $queue
     */
    public function __construct(QueueInterface $queue)
    {
        parent::__construct();
        $this->queue = $queue;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->queue->push($this->prepareMessagePayload($input));
    }

    abstract protected function prepareMessagePayload(InputInterface $input): array;
}
