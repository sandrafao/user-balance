<?php
/**
 * File contains Class TransactionFailed
 *
 * @since 10.08.2018
 */

namespace UserBalanceApp\Balance\Driver\Exception;

/**
 * Class TransactionFailed
 *
 * @package UserBalanceApp\Balance\Exception
 */
class TransactionFailed extends \Exception
{
    /**
     * @param string          $transactionId
     * @param int             $code
     * @param \Throwable|null $previous
     *
     * @return static
     */
    public static function transactionSaveError(string $transactionId, int $code = 0, \Throwable $previous = null)
    {
        $message = sprintf('Failed to save transaction with id %s', $transactionId);
        return new static($message, $code, $previous);
    }

    /**
     * @param string          $operation
     * @param int             $code
     * @param \Throwable|null $previous
     *
     * @return static
     */
    public static function operationFailed(string $operation, int $code = 0, \Throwable $previous = null)
    {
        $message = sprintf(
            'Failed to process transaction operation %s',
            $operation
        );
        return new static($message, $code, $previous);
    }
}
