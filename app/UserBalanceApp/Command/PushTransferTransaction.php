<?php
/**
 * File contains Class PushTransferTransaction
 *
 * @since 09.08.2018
 */

namespace UserBalanceApp\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Class PushTransferTransaction
 *
 * @package UserBalanceApp\Command
 */
class PushTransferTransaction extends AbstractPushTransaction
{
    /**
     * @param InputInterface $input
     *
     * @return array
     */
    protected function prepareMessagePayload(InputInterface $input): array
    {
        return [
            'operation' => 'transfer',
            'userFrom' => $input->getArgument('userFrom'),
            'userTo'   => $input->getArgument('userTo'),
            'amount'   => $input->getArgument('amount')
        ];
    }

    protected function configure()
    {
        $this->setName('transaction:push:transfer')
             ->setDescription('Push transfer transaction to queue')
             ->addArgument('userFrom', InputArgument::REQUIRED, 'Identifier of user from whom transfer')
             ->addArgument('userTo', InputArgument::REQUIRED, 'Identifier of user to whom transfer')
             ->addArgument('amount', InputArgument::REQUIRED, 'Amount for tranfer');
    }
}
