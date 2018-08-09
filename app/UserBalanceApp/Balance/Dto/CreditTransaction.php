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
     * @var int
     */
    protected $user;

    /**
     * CreditTransaction constructor.
     *
     * @param string $identifier
     * @param string $amount
     * @param int    $user
     */
    public function __construct(string $identifier, string $amount, int $user)
    {
        parent::__construct($identifier, $amount);
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getUser(): int
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getOperationType(): string
    {
        return static::OPERATION_TYPE;
    }
}
