<?php
/**
 * File contains Class PushCreditTransaction
 *
 * @since 09.08.2018
 */

namespace UserBalanceApp\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Class PushCreditTransaction
 *
 * @package UserBalanceApp\Command
 */
class PushCreditTransaction extends AbstractPushTransaction
{
    /**
     * @param InputInterface $input
     *
     * @return array
     */
    protected function prepareMessagePayload(InputInterface $input): array
    {
        return [
            'operation' => 'credit',
            'user'      => $input->getArgument('user'),
            'amount'    => $input->getArgument('amount')
        ];
    }

    protected function configure()
    {
        $this->setName('transaction:push:credit')
            ->setDescription('Push credit transaction to queue')
            ->addArgument('user', InputArgument::REQUIRED, 'Identifier of user to credit')
            ->addArgument('amount', InputArgument::REQUIRED, 'Amount for credit');
    }
}
