<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
PHPQueue\Base::$queue_namespace = '\UserBalanceApp\Queue\Queues';
PHPQueue\Base::$worker_namespace = '\UserBalanceApp\Queue\Workers';