<?php

use Silly\Application;
use UserBalanceApp\Command\RunWorkerCommand;

/** @var \Psr\Container\ContainerInterface $container */
$container = require __DIR__ . '/../bootstrap.php';

$app = new Application();
$app->useContainer($container, $injectWithTypeHint = true);

$app->addCommands(
   [
       $container->get(RunWorkerCommand::class)
   ]
);

$app->run();

