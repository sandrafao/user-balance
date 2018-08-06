<?php
/**
 * File contains Class WithdrawTransaction
 *
 * @since 06.08.2018
 */

namespace UserBalanceApp\Balance\Dto;

/**
 * Class WithdrawTransaction
 *
 * @package UserBalanceApp\Balance\Dto
 */
class WithdrawTransaction extends AbstractTransaction
{
    const OPERATION_TYPE = 'withdraw';

    /**
     * @return string
     */
    public function getOperationType(): string
    {
        return static::OPERATION_TYPE;
    }
}
