<?php
/**
 * File contains Class InvalidMessagePayload
 *
 * @since 09.08.2018
 */

namespace UserBalanceApp\Balance\Exception;

/**
 * Class InvalidMessagePayload
 *
 * @package UserBalanceApp\Balance\Exception
 */
class InvalidMessagePayload extends \RuntimeException
{
    /**
     * @param string          $valueKey
     * @param int             $code
     * @param \Throwable|null $previous
     *
     * @return static
     */
    public static function valueIsNotSet(string $valueKey, int $code = 0, \Throwable $previous = null)
    {
        $message = sprintf('Invalid message payload. Value %s is required but not present', $valueKey);
        return new static($message, $code, $previous);
    }

    /**
     * @param string          $valueKey
     * @param int             $code
     * @param \Throwable|null $previous
     *
     * @return static
     */
    public static function valueIsEmpty(string $valueKey, int $code = 0, \Throwable $previous = null)
    {
        $message = sprintf('Invalid message payload. Value %s is required but is empty', $valueKey);
        return new static($message, $code, $previous);
    }

    /**
     * @param string          $operation
     * @param int             $code
     * @param \Throwable|null $previous
     *
     * @return static
     */
    public static function unsupportedOperation(string $operation, int $code = 0, \Throwable $previous = null)
    {
        $message = sprintf('Operation %s is not supported', $operation);
        return new static($message, $code, $previous);
    }
}
