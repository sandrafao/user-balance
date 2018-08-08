<?php
/**
 * File contains Interface TransactionDriverInterface
 *
 * @since 06.08.2018
 */

namespace UserBalanceApp\Balance\Driver;

use UserBalanceApp\Balance\Dto\CreditTransaction;
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
     * @param WithdrawTransaction $withdrawTransaction
     * @param CreditTransaction   $creditTransaction
     *
     * @return void
     */
    public function transfer(WithdrawTransaction $withdrawTransaction, CreditTransaction $creditTransaction);
}
