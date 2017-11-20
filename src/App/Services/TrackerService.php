<?php

namespace App\Services;

use Doctrine\DBAL\Connection;
use EXS\RabbitmqProvider\Services\PostmanService;
use EXS\RabbitmqProvider\Services\ConsumerService;

/**
 * Class UserService
 * @package App\Services
 */
class TrackerService extends BaseService
{
    /**
     * @var PostmanService
     */
    public $postman;

    /**
     * @var ConsumerService
     */
    public $consumer;

    /**
     * TrackerService constructor.
     * @param Connection $db
     * @param PostmanService $postman
     * @param ConsumerService $consumer
     */
    public function __construct(Connection $db, PostmanService $postman, ConsumerService $consumer)
    {
        parent::__construct($db);
        $this->postman = $postman;
        $this->consumer = $consumer;
    }

    /**
     * Publish messages to the new exchange queue
     *
     * @param array $event
     * @return void
     */
    public function publish($event)
    {
        $message = json_encode($event);
        $this->postman->publish($message, false);
    }

    /**
     * Get all messages from the queue
     * @return void
     */
    public function consume()
    {
        $messages = $this->consumer->consumeAll();


//        $this->db->insert('trackers', $events);
    }
}
