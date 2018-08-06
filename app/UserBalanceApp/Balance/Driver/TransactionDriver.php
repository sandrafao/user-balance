<?php
/**
 * File contains Class TransactionService
 *
 * @since 06.08.2018
 */

namespace UserBalanceApp\Balance\Driver;

use PDO;
use UserBalanceApp\Balance\Dto\AbstractTransaction;
use UserBalanceApp\Balance\Dto\CreditTransaction;
use UserBalanceApp\Balance\Dto\WithdrawTransaction;

/**
 * Class TransactionService
 *
 * @package UserBalanceApp
 */
class TransactionDriver
{
    /**
     * @var \PDO
     */
    protected $connection;

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
            $this->creditBalance($transaction);
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
            $this->withdrawBalance($transaction);
            $this->connection->commit();
        } catch (\Throwable $exception) {
            $this->connection->rollBack();
            throw $exception;
        }
    }

    /**
     * @param WithdrawTransaction $withdrawTransaction
     * @param CreditTransaction   $creditTransaction
     *
     * @throws \Throwable
     */
    public function transfer(
        WithdrawTransaction $withdrawTransaction,
        CreditTransaction $creditTransaction
    ) {
        $this->connection->beginTransaction();
        try {
            $this->insertTransaction($withdrawTransaction);
            $this->withdrawBalance($withdrawTransaction);
            $this->insertTransaction($creditTransaction);
            $this->creditBalance($creditTransaction);
            $this->connection->commit();
        } catch (\Throwable $exception) {
            $this->connection->rollBack();
            throw $exception;
        }
    }

    /**
     * @param AbstractTransaction $transaction
     */
    protected function insertTransaction(AbstractTransaction $transaction)
    {
        try {
            $query = "
INSERT INTO `transactions` (`user_identifier`, `ammount`, `operation_type`, `created_at`)
  VALUES (:userIdentifier, :amount, :operation, NOW())
";

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue('userIdentifier', $transaction->getUserIdentifier(), PDO::PARAM_INT);
            $stmt->bindValue('amount', $transaction->getAmountFormatted(), PDO::PARAM_STR);
            $stmt->bindValue('operation', $transaction->getOperationType(), PDO::PARAM_STR);
            $result = $stmt->execute();
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
            throw new \RuntimeException('Failed to save transaction operation', 0, $exception);
        }
        if ($stmt->rowCount() < 1) {
            throw new \RuntimeException('Failed to save transaction operation');
        }
    }

    /**
     * @param CreditTransaction $transaction
     */
    protected function creditBalance(CreditTransaction $transaction)
    {
        try {
            $query = "
INSERT INTO `user_balance` (`user_identifier`, `balance`, `last_modified`) VALUES (:userIdentifier, :amount, NOW())
  ON DUPLICATE KEY UPDATE `balance` = `balance` + :amount
";
            $stmt = $this->connection->prepare($query);
            $result = $stmt->execute(
                [
                    'userIdentifier' => $transaction->getUserIdentifier(),
                    'amount'         => $transaction->getAmountFormatted(),
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
            throw new \RuntimeException('Failed to credit user balance', 0, $exception);
        }

        if ($stmt->rowCount() < 1) {
            throw new \LogicException('Failed to credit user balance');
        }
    }

    /**
     * @param WithdrawTransaction $transaction
     */
    protected function withdrawBalance(WithdrawTransaction $transaction)
    {
        try {
            $query = "
UPDATE `user_balance` SET `balance` = `balance` - :amount, `last_modified` = NOW() 
  WHERE `user_identifier` = :userIdentifier AND `balance` >= :amount
";
            $stmt = $this->connection->prepare($query);
            $result = $stmt->execute(
                [
                    'userIdentifier' => $transaction->getUserIdentifier(),
                    'amount'         => $transaction->getAmountFormatted(),
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
            throw new \RuntimeException('Failed to withdraw user balance', 0, $exception);
        }

        if ($stmt->rowCount() < 1) {
            throw new \LogicException('Failed to withdraw user balance');
        }
    }
}
