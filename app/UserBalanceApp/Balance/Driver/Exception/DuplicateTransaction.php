<?php
/**
 * File contains Class DuplicateTransaction
 *
 * @since 10.08.2018
 */

namespace UserBalanceApp\Balance\Driver\Exception;

/**
 * Class DuplicateTransaction
 *
 * @package UserBalanceApp\Balance\Exception
 */
class DuplicateTransaction extends \LogicException
{
    /**
     * @param string          $transactionId
     * @param int             $code
     * @param \Throwable|null $previous
     *
     * @return static
     */
    public static function duplicate(string $transactionId, int $code = 0, \Throwable $previous = null)
    {
        $message = sprintf('Duplicate transaction with id %s', $transactionId);
        return new static($message, $code, $previous);
    }
}
