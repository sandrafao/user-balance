<?php
/**
 * File contains Class TransactionService
 *
 * @since 06.08.2018
 */

namespace UserBalanceApp\Balance\Service;

use UserBalanceApp\Balance\Driver\TransactionDriverInterface;
use UserBalanceApp\Balance\Dto\CreditTransaction;
use UserBalanceApp\Balance\Dto\WithdrawTransaction;

/**
 * Class TransactionService
 *
 * @package UserBalanceApp\Balance\Service
 */
class TransactionService
{
    /**
     * @var TransactionDriverInterface
     */
    protected $driver;

    /**
     * TransactionService constructor.
     *
     * @param TransactionDriverInterface $driver
     */
    public function __construct(TransactionDriverInterface $driver)
    {
        $this->driver = $driver;
    }

    public function runTransaction(array $data)
    {
        $operation = $this->extractValue('operation', $data);
        if ($operation === 'credit') {
            $this->runCreditTransaction($data);
        } elseif ($operation === 'withdraw') {
            $this->runWithdrawTransaction($data);
        } elseif ($operation === 'transfer') {
            $this->runTransferTransaction($data);
        } else {
            throw new \RuntimeException('Unsupported transaction type');
        }
    }

    /**
     * @param array $data
     */
    protected function runCreditTransaction(array $data)
    {
        $transaction = new CreditTransaction(
            $this->extractValue('user', $data),
            $this->extractValue('amount', $data)
        );
        $this->driver->credit($transaction);
    }

    /**
     * @param array $data
     */
    protected function runWithdrawTransaction(array $data)
    {
        $transaction = new WithdrawTransaction(
            $this->extractValue('user', $data),
            $this->extractValue('amount', $data)
        );
        $this->driver->withdraw($transaction);
    }

    /**
     * @param array $data
     */
    protected function runTransferTransaction(array $data)
    {
        $amount = $this->extractValue('amount', $data);
        $transactionWithdraw = new WithdrawTransaction(
            $this->extractValue('userFrom', $data),
            $amount
        );
        $transactionCredit = new CreditTransaction(
            $this->extractValue('userTo', $data),
            $amount
        );
        $this->driver->transfer($transactionWithdraw, $transactionCredit);
    }

    /**
     * @param string $key
     * @param array  $data
     *
     * @return mixed
     */
    protected function extractValue(string $key, array $data)
    {
        if (!isset($data[$key])) {
            throw new \RuntimeException(sprintf('Value %s is not set', $key));
        }

        if (empty($data[$key])) {
            throw new \RuntimeException(sprintf('Value %s is empty', $key));
        }

        return $data[$key];
    }
}
