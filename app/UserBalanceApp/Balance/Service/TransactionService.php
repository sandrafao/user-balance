<?php
/**
 * File contains Class TransactionService
 *
 * @since 06.08.2018
 */

namespace UserBalanceApp\Balance\Service;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use UserBalanceApp\Balance\Driver\Exception\DuplicateTransaction;
use UserBalanceApp\Balance\Driver\TransactionDriverInterface;
use UserBalanceApp\Balance\Dto\CreditTransaction;
use UserBalanceApp\Balance\Dto\TransferTransaction;
use UserBalanceApp\Balance\Dto\WithdrawTransaction;
use UserBalanceApp\Balance\Event\FailedTransactionEvent;
use UserBalanceApp\Balance\Event\SuccessTransactionEvent;
use UserBalanceApp\Balance\Exception\InvalidMessagePayload;
use UserBalanceApp\Balance\Exception\RetryTransaction;

/**
 * Class TransactionService
 *
 * @package UserBalanceApp\Balance\Service
 */
class TransactionService
{
    /**
     * @var TransactionDriverInterface
     */
    protected $driver;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * TransactionService constructor.
     *
     * @param TransactionDriverInterface $driver
     * @param EventDispatcherInterface   $eventDispatcher
     */
    public function __construct(
        TransactionDriverInterface $driver,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->driver          = $driver;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param array $data
     */
    public function runTransaction(array $data)
    {
        $identifier = (string)$this->extractValue('transaction_id', $data);
        if ($this->driver->retryCount($identifier, 5) === false) {
            throw new \RuntimeException('Maximum allowed retries exceeded');
        }
        $operation = $this->extractValue('operation', $data);
        if ($operation === CreditTransaction::OPERATION_TYPE) {
            $this->runCreditTransaction($identifier, $data);
        } elseif ($operation === WithdrawTransaction::OPERATION_TYPE) {
            $this->runWithdrawTransaction($identifier, $data);
        } elseif ($operation === TransferTransaction::OPERATION_TYPE) {
            $this->runTransferTransaction($identifier, $data);
        } else {
            throw InvalidMessagePayload::unsupportedOperation($operation);
        }
    }

    /**
     * @param string $identifier
     * @param array  $data
     *
     * @throws RetryTransaction
     * @throws \Throwable
     */
    protected function runCreditTransaction(string $identifier, array $data)
    {
        $transaction = new CreditTransaction(
            $identifier,
            (string)$this->extractValue('amount', $data),
            (int)$this->extractValue('user', $data)
        );
        try {
            $this->driver->credit($transaction);
        } catch (\Throwable $exception) {
            $event = new FailedTransactionEvent($transaction);
            $this->eventDispatcher->dispatch(FailedTransactionEvent::NAME, $event);
            if (!$exception instanceof DuplicateTransaction) {
                $exception = new RetryTransaction('Need to retry transaction', 0, $exception);
            }
            throw $exception;
        }
        $this->driver->clearRetries($transaction->getIdentifier());
        $event = new SuccessTransactionEvent($transaction);
        $this->eventDispatcher->dispatch(SuccessTransactionEvent::NAME, $event);
    }

    /**
     * @param string $identifier
     * @param array  $data
     *
     * @throws RetryTransaction
     * @throws \Throwable
     */
    protected function runWithdrawTransaction(string $identifier, array $data)
    {
        $transaction = new WithdrawTransaction(
            $identifier,
            (string)$this->extractValue('amount', $data),
            (int)$this->extractValue('user', $data)
        );
        try {
            $this->driver->withdraw($transaction);
        } catch (\Throwable $exception) {
            $event = new FailedTransactionEvent($transaction);
            $this->eventDispatcher->dispatch(FailedTransactionEvent::NAME, $event);
            if (!$exception instanceof DuplicateTransaction) {
                $exception = new RetryTransaction('Need to retry transaction', 0, $exception);
            }
            throw $exception;
        }
        $this->driver->clearRetries($transaction->getIdentifier());
        $event = new SuccessTransactionEvent($transaction);
        $this->eventDispatcher->dispatch(SuccessTransactionEvent::NAME, $event);
    }

    /**
     * @param string $identifier
     * @param array  $data
     *
     * @throws RetryTransaction
     * @throws \Throwable
     */
    protected function runTransferTransaction(string $identifier, array $data)
    {
        $transaction = new TransferTransaction(
            $identifier,
            (string)$this->extractValue('amount', $data),
            (int)$this->extractValue('userFrom', $data),
            (int)$this->extractValue('userTo', $data)
        );
        try {
            $this->driver->transfer($transaction);
        } catch (\Throwable $exception) {
            $event = new FailedTransactionEvent($transaction);
            $this->eventDispatcher->dispatch(FailedTransactionEvent::NAME, $event);
            if (!$exception instanceof DuplicateTransaction) {
                $exception = new RetryTransaction('Need to retry transaction', 0, $exception);
            }
            throw $exception;
        }
        $this->driver->clearRetries($transaction->getIdentifier());
        $event = new SuccessTransactionEvent($transaction);
        $this->eventDispatcher->dispatch(SuccessTransactionEvent::NAME, $event);
    }

    /**
     * @param string $key
     * @param array  $data
     *
     * @return mixed
     */
    protected function extractValue(string $key, array $data)
    {
        if (!isset($data[$key])) {
            throw InvalidMessagePayload::valueIsNotSet($key);
        }

        if (empty($data[$key])) {
            throw InvalidMessagePayload::valueIsEmpty($key);
        }

        return $data[$key];
    }
}
