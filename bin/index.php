<?php

use Silly\Application;
use UserBalanceApp\Command\PushCreditTransaction;
use UserBalanceApp\Command\PushTransferTransaction;
use UserBalanceApp\Command\PushWithdrawTransaction;
use UserBalanceApp\Command\RunWorker;

/** @var \Psr\Container\ContainerInterface $container */
$container = require __DIR__ . '/../bootstrap.php';

$app = new Application();
$app->useContainer($container, $injectWithTypeHint = true);

$app->addCommands(
   [
       $container->get(RunWorker::class),
       $container->get(PushCreditTransaction::class),
       $container->get(PushWithdrawTransaction::class),
       $container->get(PushTransferTransaction::class),
   ]
);

$app->run();

