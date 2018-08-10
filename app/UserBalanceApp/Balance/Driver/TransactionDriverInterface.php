<?php
/**
 * File contains Interface TransactionDriverInterface
 *
 * @since 06.08.2018
 */

namespace UserBalanceApp\Balance\Driver;

use UserBalanceApp\Balance\Dto\CreditTransaction;
use UserBalanceApp\Balance\Dto\TransferTransaction;
use UserBalanceApp\Balance\Dto\WithdrawTransaction;

/**
 * Interface TransactionDriverInterface
 *
 * @package UserBalanceApp\Balance\Driver
 */
interface TransactionDriverInterface
{
    /**
     * @param CreditTransaction $transaction
     *
     * @return void
     */
    public function credit(CreditTransaction $transaction);

    /**
     * @param WithdrawTransaction $transaction
     *
     * @return void
     */
    public function withdraw(WithdrawTransaction $transaction);

    /**
     * @param TransferTransaction $transaction
     *
     * @return void
     */
    public function transfer(TransferTransaction $transaction);

    /**
     * @param string $transactionId
     * @param int    $maxRetries
     *
     * @return bool
     */
    public function retryCount(string $transactionId, int $maxRetries): bool;

    /**
     * @param string $transactionId
     *
     * @return void
     */
    public function clearRetries(string $transactionId);
}
