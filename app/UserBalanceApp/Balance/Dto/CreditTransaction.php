<?php
/**
 * File contains Class CreditTransaction
 *
 * @since 06.08.2018
 */

namespace UserBalanceApp\Balance\Dto;

/**
 * Class CreditTransaction
 *
 * @package UserBalanceApp\Balance\Dto
 */
class CreditTransaction extends AbstractTransaction
{
    const OPERATION_TYPE = 'credit';

    /**
     * @return string
     */
    public function getOperationType(): string
    {
        return static::OPERATION_TYPE;
    }
}
