<?php
/**
 * File contains Class FailedTransactionEvent
 *
 * @since 10.08.2018
 */

namespace UserBalanceApp\Balance\Event;

/**
 * Class FailedTransactionEvent
 *
 * @package UserBalanceApp\Balance\Event
 */
class FailedTransactionEvent extends AbstractBaseTransactionEvent
{
    const NAME = 'transaction-failed';
}
