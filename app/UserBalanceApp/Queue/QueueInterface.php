<?php
/**
 * File contains Class QueueInterface
 *
 * @since 09.08.2018
 */

namespace UserBalanceApp\Queue;

/**
 * Interface QueueInterface
 *
 * @package UserBalanceApp\Queue
 */
interface QueueInterface
{
    /**
     * @param array $payload
     *
     * @return void
     */
    public function push(array $payload);
}