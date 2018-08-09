<?php
/**
 * File contains Class AbstractBaseTransactionEvent
 *
 * @since 10.08.2018
 */

namespace UserBalanceApp\Balance\Event;

use Symfony\Component\EventDispatcher\Event;
use UserBalanceApp\Balance\Dto\AbstractTransaction;

/**
 * Class AbstractBaseTransactionEvent
 *
 * @package UserBalanceApp\Balance\Event
 */
abstract class AbstractBaseTransactionEvent extends Event
{
    /**
     * @var AbstractTransaction
     */
    protected $transaction;

    /**
     * SuccessTransactionEvent constructor.
     *
     * @param AbstractTransaction $transaction
     */
    public function __construct(AbstractTransaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * @return AbstractTransaction
     */
    public function getTransaction(): AbstractTransaction
    {
        return $this->transaction;
    }
}
