<?php
/**
 * File contains Class TransactionService
 *
 * @since 06.08.2018
 */

namespace UserBalanceApp\Balance\Driver;

use PDO;
use UserBalanceApp\Balance\Driver\Exception\DuplicateTransaction;
use UserBalanceApp\Balance\Driver\Exception\TransactionFailed;
use UserBalanceApp\Balance\Dto\AbstractTransaction;
use UserBalanceApp\Balance\Dto\CreditTransaction;
use UserBalanceApp\Balance\Dto\TransferTransaction;
use UserBalanceApp\Balance\Dto\WithdrawTransaction;

/**
 * Class TransactionService
 *
 * @package UserBalanceApp
 */
class TransactionDriver implements TransactionDriverInterface
{
    /**
     * @var \PDO
     */
    protected $connection;

    /**
     * TransactionDriver constructor.
     *
     * @param PDO $connection
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param CreditTransaction $transaction
     *
     * @throws \Throwable
     */
    public function credit(CreditTransaction $transaction)
    {
        $this->connection->beginTransaction();
        try {
            $this->insertTransaction($transaction);
            $this->creditBalance($transaction->getUser(), $transaction->getAmount());
            $this->connection->commit();
        } catch (\Throwable $exception) {
            $this->connection->rollBack();
            throw $exception;
        }
    }

    /**
     * @param WithdrawTransaction $transaction
     *
     * @throws \Throwable
     */
    public function withdraw(WithdrawTransaction $transaction)
    {
        $this->connection->beginTransaction();
        try {
            $this->insertTransaction($transaction);
            $this->withdrawBalance($transaction->getUser(), $transaction->getAmount());
            $this->connection->commit();
        } catch (\Throwable $exception) {
            $this->connection->rollBack();
            throw $exception;
        }
    }

    /**
     * @param TransferTransaction $transaction
     *
     * @throws \Throwable
     */
    public function transfer(TransferTransaction $transaction) {
        $this->connection->beginTransaction();
        try {
            $this->insertTransaction($transaction);
            $this->withdrawBalance($transaction->getUserFrom(), $transaction->getAmount());
            $this->creditBalance($transaction->getUserTo(), $transaction->getAmount());
            $this->connection->commit();
        } catch (\Throwable $exception) {
            $this->connection->rollBack();
            throw $exception;
        }
    }

    /**
     * @param string $transactionId
     * @param int    $maxRetries
     *
     * @return bool
     */
    public function retryCount(string $transactionId, int $maxRetries): bool
    {
        $query = "
INSERT INTO `retry_counter` (`transaction_id`, `retries`) VALUES (:transaction_id, 0) 
ON DUPLICATE KEY UPDATE `retries` = `retries` + 1
";
        $stmt = $this->connection->prepare($query);
        $stmt->bindValue('transaction_id', $transactionId, PDO::PARAM_STR);
        $stmt->execute();

        $query = "SELECT `retries` FROM `retry_counter` 
WHERE `transaction_id` = :transaction_id AND `retries` < :max_retires";
        $stmt = $this->connection->prepare($query);
        $stmt->bindValue('transaction_id', $transactionId, PDO::PARAM_STR);
        $stmt->bindValue('max_retires', $maxRetries, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchColumn();
        if ($result === false) {
            $this->clearRetries($transactionId);
            return false;
        }
        return true;
    }

    /**
     * @param string $transactionId
     */
    public function clearRetries(string $transactionId)
    {
        $query = "DELETE FROM `retry_counter` WHERE `transaction_id` = :transaction_id";
        $stmt = $this->connection->prepare($query);
        $stmt->bindValue('transaction_id', $transactionId, PDO::PARAM_STR);
        $stmt->execute();
    }

    /**
     * @param AbstractTransaction $transaction
     *
     * @throws TransactionFailed
     */
    protected function insertTransaction(AbstractTransaction $transaction)
    {
        try {
            $query = "
INSERT INTO `transactions` (`identifier`, `user_to`, `user_from`, `ammount`, `operation_type`, `status`, `created_at`)
  VALUES (:identifier, :userTo, :userFrom, :amount, :operation, :status, NOW()) 
";

            $userTo   = null;
            $userFrom = null;
            if ($transaction instanceof TransferTransaction) {
                $userTo   = $transaction->getUserTo();
                $userFrom = $transaction->getUserFrom();
            } elseif ($transaction instanceof CreditTransaction) {
                $userTo = $transaction->getUser();
            } elseif ($transaction instanceof WithdrawTransaction) {
                $userFrom = $transaction->getUser();
            }
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue('identifier', $transaction->getIdentifier(), PDO::PARAM_STR);
            $stmt->bindValue('userTo', $userTo, PDO::PARAM_INT);
            $stmt->bindValue('userFrom', $userFrom, PDO::PARAM_INT);
            $stmt->bindValue('amount', $transaction->getAmountFormatted(), PDO::PARAM_STR);
            $stmt->bindValue('operation', $transaction->getOperationType(), PDO::PARAM_STR);
            $stmt->bindValue('status', AbstractTransaction::STATUS_NEW);

            $result = $stmt->execute();
            if ($result === false) {
                if ($stmt->errorCode() === '23000') {
                    throw DuplicateTransaction::duplicate($transaction->getIdentifier());
                }
                throw new \RuntimeException(
                    sprintf(
                        'Error executing statement. Code: %s. Info: %s',
                        $stmt->errorCode(),
                        var_export($stmt->errorInfo(), true)
                    )
                );
            }
        } catch (DuplicateTransaction $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw TransactionFailed::transactionSaveError($transaction->getIdentifier(), 0, $exception);
        }
        if ($stmt->rowCount() < 1) {
            throw TransactionFailed::transactionSaveError($transaction->getIdentifier());
        }
    }

    /**
     * @param int    $userIdentifier
     * @param string $amount
     *
     * @throws TransactionFailed
     */
    protected function creditBalance(int $userIdentifier, string $amount)
    {
        try {
            $query = "
INSERT INTO `user_balance` (`user_identifier`, `balance`, `last_modified`) VALUES (:userIdentifier, :amount, NOW())
  ON DUPLICATE KEY UPDATE `balance` = `balance` + :amount
";
            $stmt = $this->connection->prepare($query);
            $result = $stmt->execute(
                [
                    'userIdentifier' => $userIdentifier,
                    'amount'         => $amount,
                ]
            );
            if ($result === false) {
                throw new \RuntimeException(
                    sprintf(
                        'Error executing statement. Code: %s. Info: %s',
                        $stmt->errorCode(),
                        var_export($stmt->errorInfo(), true)
                    )
                );
            }
        } catch (\Throwable $exception) {
            throw TransactionFailed::operationFailed('credit', 0, $exception);
        }

        if ($stmt->rowCount() < 1) {
            throw TransactionFailed::operationFailed('credit');
        }
    }

    /**
     * @param int    $userIdentifier
     * @param string $amount
     *
     * @throws TransactionFailed
     */
    protected function withdrawBalance(int $userIdentifier, string $amount)
    {
        try {
            $query = "
UPDATE `user_balance` SET `balance` = `balance` - :amount, `last_modified` = NOW() 
  WHERE `user_identifier` = :userIdentifier AND `balance` >= :amount
";
            $stmt = $this->connection->prepare($query);
            $result = $stmt->execute(
                [
                    'userIdentifier' => $userIdentifier,
                    'amount'         => $amount,
                ]
            );
            if ($result === false) {
                throw new \RuntimeException(
                    sprintf(
                        'Error executing statement. Code: %s. Info: %s',
                        $stmt->errorCode(),
                        var_export($stmt->errorInfo(), true)
                    )
                );
            }
        } catch (\Throwable $exception) {
            throw TransactionFailed::operationFailed('withdraw', 0, $exception);
        }

        if ($stmt->rowCount() < 1) {
            throw TransactionFailed::operationFailed('withdraw');
        }
    }
}
