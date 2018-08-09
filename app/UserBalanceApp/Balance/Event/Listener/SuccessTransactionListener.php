<?php
/**
 * File contains Class SuccessTransactionListener
 *
 * @since 10.08.2018
 */

namespace UserBalanceApp\Balance\Event\Listener;

use Psr\Log\LoggerInterface;
use UserBalanceApp\Balance\Event\SuccessTransactionEvent;

/**
 * Class SuccessTransactionListener
 *
 * @package UserBalanceApp\Event\Listener
 */
class SuccessTransactionListener
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * SuccessTransactionListener constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param SuccessTransactionEvent $event
     */
    public function onSuccessTransaction(SuccessTransactionEvent $event)
    {
        $this->logger->info(
            'Transaction processed successfully',
            ['transaction' => $event->getTransaction()]
        );
    }
}
