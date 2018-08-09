<?php
/**
 * File contains Class SuccessTransactionEvent
 *
 * @since 10.08.2018
 */

namespace UserBalanceApp\Balance\Event;

/**
 * Class SuccessTransactionEvent
 *
 * @package UserBalanceApp\Event
 */
class SuccessTransactionEvent extends AbstractBaseTransactionEvent
{
    const NAME = 'transaction-success';
}
