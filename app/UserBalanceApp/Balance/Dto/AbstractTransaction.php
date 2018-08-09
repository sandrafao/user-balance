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
    const STATUS_NEW     = 'new';
    const STATUS_PENDING = 'pending';
    const STATUS_DONE    = 'done';

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $amount;

    /**
     * @var string
     */
    protected $operationType;

    /**
     * @var string
     */
    protected $status;

    /**
     * AbstractTransaction constructor.
     *
     * @param string $identifier
     * @param string $amount
     */
    public function __construct(string $identifier, string $amount)
    {
        $this->identifier = $identifier;
        $this->amount     = $amount;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
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
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus(string $status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    abstract public function getOperationType(): string;
}
