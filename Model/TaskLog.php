<?php

namespace BobrD\SchedulerBundle\Model;

use Ramsey\Uuid\UuidInterface;
use BobrD\SchedulerBundle\Event\TaskFinishedEvent;

class TaskLog
{
    const EXECUTE_STATUS_SUCCESS = TaskFinishedEvent::STATUS_SUCCESS;

    const EXECUTE_STATUS_FAIL = TaskFinishedEvent::STATUS_FAIL;

    /**
     * @var UuidInterface
     */
    private $uuid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $cronTime;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \DateTime
     */
    private $startedAt;

    /**
     * @var \DateTime|null
     */
    private $finishedAt;

    /**
     * @var bool
     */
    private $started;

    /**
     * @var null|string
     */
    private $executeStatus;

    /**
     * @var null|string
     */
    private $output;

    /**
     * @var null|string
     */
    private $stackTrace;

    /**
     * @param UuidInterface $uuid
     * @param string        $name
     * @param string        $description
     * @param string        $cronTime
     */
    public function __construct(UuidInterface $uuid, $name, $description, $cronTime)
    {
        $this->startedAt = new \DateTime();
        $this->uuid = $uuid;
        $this->started = true;
        $this->name = $name;
        $this->description = $description;
        $this->cronTime = $cronTime;
    }

    /**
     * @return UuidInterface
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getCronTime()
    {
        return $this->cronTime;
    }

    /**
     * @return \DateTime
     */
    public function getStartedAt()
    {
        return clone $this->startedAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getFinishedAt()
    {
        if (null === $this->finishedAt) {
            return;
        }

        return clone $this->finishedAt;
    }

    /**
     * @param bool        $success
     * @param string|null $output
     * @param string|null $stackTrace
     */
    public function markAsFinished($success = true, $output = null, $stackTrace = null)
    {
        $this->finishedAt = new \DateTime();
        $this->started = false;

        $this->executeStatus = $success ? self::EXECUTE_STATUS_SUCCESS : self::EXECUTE_STATUS_FAIL;

        $this->output = $output;
        $this->stackTrace = $stackTrace;
    }

    /**
     * @return bool
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * @return bool
     */
    public function isFinished()
    {
        return !$this->isStarted();
    }

    /**
     * If task not finished return null.
     *
     * @return null|bool
     */
    public function isFinishedSuccessful()
    {
        if ($this->isStarted()) {
            return;
        }

        return $this->executeStatus === self::EXECUTE_STATUS_SUCCESS;
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
    public function getStackTrace()
    {
        return $this->stackTrace;
    }

    /**
     * @return \DateInterval|null
     */
    public function getExecutedTime()
    {
        if ($this->isStarted()) {
            return;
        }

        return $this->startedAt->diff($this->finishedAt);
    }
}
