<?php

namespace BobrD\SchedulerBundle\Event;

use Ramsey\Uuid\UuidInterface;
use BobrD\SchedulerBundle\Services\Scheduler\TaskInterface;
use Symfony\Component\EventDispatcher\Event;

class TaskFinishedEvent extends Event
{
    const STATUS_SUCCESS = 'success';

    const STATUS_FAIL = 'fail';

    /**
     * @var UuidInterface
     */
    private $uuid;

    /**
     * @var TaskInterface
     */
    private $task;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $output;

    /**
     * @var null|string
     */
    private $exceptionTrace;

    /**
     * @param UuidInterface $uuid
     * @param TaskInterface $task
     * @param string        $status
     * @param null|string   $output
     * @param null|string   $exceptionTrace
     */
    public function __construct(
        UuidInterface $uuid,
        TaskInterface $task,
        $status,
        $output = null,
        $exceptionTrace = null
    ) {
        $this->uuid = $uuid;
        $this->task = $task;
        $this->status = $status;
        $this->output = $output;
        $this->exceptionTrace = $exceptionTrace;
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

    /**
     * @return bool
     */
    public function isFinishedSuccessful()
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    /**
     * @return null|string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return null|string
     */
    public function getExceptionTrace()
    {
        return $this->exceptionTrace;
    }
}
