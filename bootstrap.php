<?php

use DI\ContainerBuilder;
use Dotenv\Dotenv;

require __DIR__ . '/vendor/autoload.php';

$dotenv = new Dotenv(__DIR__);
$dotenv->load();

$containerBuilder = new ContainerBuilder;
$containerBuilder->addDefinitions(__DIR__ . '/config/container.php');
$container = $containerBuilder->build();
return $container;