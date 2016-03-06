<?php

namespace BobrD\SchedulerBundle\Event;

use Ramsey\Uuid\UuidInterface;
use BobrD\SchedulerBundle\Services\Scheduler\TaskInterface;
use Symfony\Component\EventDispatcher\Event;

class TaskStartedEvent extends Event
{
    /**
     * @var UuidInterface
     */
    private $uuid;

    /**
     * @var TaskInterface
     */
    private $task;

    /**
     * @param UuidInterface $uuid
     * @param TaskInterface $task
     */
    public function __construct(UuidInterface $uuid, TaskInterface $task)
    {
        $this->uuid = $uuid;
        $this->task = $task;
    }

    /**
     * @return UuidInterface
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @return TaskInterface
     */
    public function getTask()
    {
        return $this->task;
    }
}
