<?php

use UserBalanceApp\Balance\Driver\TransactionDriver;
use UserBalanceApp\Balance\Dto\CreditTransaction;
use UserBalanceApp\Balance\Dto\WithdrawTransaction;

chdir(dirname(__DIR__));

include __DIR__ . '/../vendor/autoload.php';

$dto = new CreditTransaction(1, 10.01);
$connection = new \PDO('mysql:dbname=user_balance;host=mysql;port=3306;charset=utf8', 'app_user', 'N@ZFVG8JB+gH$w+6');
$driver = new TransactionDriver($connection);
$driver->credit($dto);

$dto = new CreditTransaction(1, 10.01);
$driver->credit($dto);

$dto = new WithdrawTransaction(1, 5.01);
$driver->withdraw($dto);

//$dto = new WithdrawTransaction(1, 10005.01);
//$driver->withdraw($dto);


$dto1 = new WithdrawTransaction(2, 5.01);
$dto2 = new CreditTransaction(3, 5.01);
$driver->transfer($dto1, $dto2);