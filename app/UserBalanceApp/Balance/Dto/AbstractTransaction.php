<?php
/**
 * File contains Class AbstractTransaction
 *
 * @since 06.08.2018
 */

namespace UserBalanceApp\Balance\Dto;

/**
 * Class AbstractTransaction
 *
 * @package UserBalanceApp\Balance\Dto
 */
abstract class AbstractTransaction
{
    /**
     * @var int
     */
    protected $userIdentifier;

    /**
     * @var string
     */
    protected $amount;

    /**
     * @var string
     */
    protected $operationType;

    /**
     * TransactionTo constructor.
     *
     * @param int    $userIdentifier
     * @param string $amount
     */
    public function __construct(int $userIdentifier, string $amount)
    {
        $this->userIdentifier = $userIdentifier;
        $this->amount         = $amount;
    }

    /**
     * @return int
     */
    public function getUserIdentifier(): int
    {
        return $this->userIdentifier;
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getAmountFormatted(): string
    {
        return sprintf('%0.4f', $this->getAmount());
    }

    /**
     * @return string
     */
    abstract public function getOperationType(): string;
}
