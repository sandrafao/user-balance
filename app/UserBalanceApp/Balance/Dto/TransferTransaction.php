<?php
/**
 * File contains Class TransferTransaction
 *
 * @since 09.08.2018
 */

namespace UserBalanceApp\Balance\Dto;

/**
 * Class TransferTransaction
 *
 * @package UserBalanceApp\Balance\Dto
 */
class TransferTransaction extends AbstractTransaction
{
    const OPERATION_TYPE = 'transfer';

    /**
     * @var int
     */
    protected $userFrom;

    /**
     * @var int
     */
    protected $userTo;

    /**
     * TransferTransaction constructor.
     *
     * @param string $identifier
     * @param string $amount
     * @param int    $userFrom
     * @param int    $userTo
     */
    public function __construct(string $identifier, string $amount, int $userFrom, int $userTo)
    {
        parent::__construct($identifier, $amount);
        $this->userFrom = $userFrom;
        $this->userTo   = $userTo;
    }

    /**
     * @return int
     */
    public function getUserFrom(): int
    {
        return $this->userFrom;
    }

    /**
     * @return int
     */
    public function getUserTo(): int
    {
        return $this->userTo;
    }

    /**
     * @return string
     */
    public function getOperationType(): string
    {
        return static::OPERATION_TYPE;
    }
}
