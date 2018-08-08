<?php
/**
 * File contains Class RunWorker
 *
 * @since 08.08.2018
 */

namespace UserBalanceApp\Command;

use Silly\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use UserBalanceApp\Worker\TransactionWorker;

/**
 * Class RunWorker
 *
 * @package UserBalanceApp\Command
 */
class RunWorker extends Command
{
    /**
     * @var TransactionWorker
     */
    protected $worker;

    /**
     * RunWorkerCommand constructor.
     *
     * @param TransactionWorker $worker
     */
    public function __construct(TransactionWorker $worker)
    {
        parent::__construct();
        $this->worker = $worker;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->worker->run();
        return true;
    }

    protected function configure()
    {
        $this->setName('app:run-transaction-worker')
            ->setDescription('Run worker to process transactions queue')
            ->setHelp('To exit press CTRL+C');
    }
}
