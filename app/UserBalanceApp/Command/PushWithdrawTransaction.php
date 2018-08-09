<?php
/**
 * File contains Class PushWithdrawTransaction
 *
 * @since 09.08.2018
 */

namespace UserBalanceApp\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Class PushWithdrawTransaction
 *
 * @package UserBalanceApp\Command
 */
class PushWithdrawTransaction extends AbstractPushTransaction
{
    /**
     * @param InputInterface $input
     *
     * @return array
     */
    protected function prepareMessagePayload(InputInterface $input): array
    {
        return [
            'transaction_id' => $input->getArgument('transaction_id'),
            'operation'      => 'withdraw',
            'user'           => $input->getArgument('user'),
            'amount'         => $input->getArgument('amount'),
        ];
    }

    protected function configure()
    {
        $this->setName('transaction:push:withdraw')
             ->setDescription('Push withdraw transaction to queue')
             ->addArgument('transaction_id', InputArgument::REQUIRED, 'Transaction identifier')
             ->addArgument('user', InputArgument::REQUIRED, 'Identifier of user to withdraw')
             ->addArgument('amount', InputArgument::REQUIRED, 'Amount for withdraw');
    }
}
