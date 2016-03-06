<?php

namespace BobrD\SchedulerBundle\Repository;

use BobrD\SchedulerBundle\Model\TaskLog;
use BobrD\SchedulerBundle\Exception\SchedulerBundleException;
use Ramsey\Uuid\UuidInterface;

interface TaskLogRepositoryInterface
{
    /**
     * @param UuidInterface $uuid
     *
     * @return TaskLog
     *
     * @throws SchedulerBundleException
     */
    public function getByUuid(UuidInterface $uuid);

    /**
     * @return TaskLog[]
     */
    public function findAll();

    /**
     * @return TaskLog[]
     */
    public function findLastHour();

    /**
     * @return TaskLog[]
     */
    public function findLastDay();

    /**
     * @return TaskLog[]
     */
    public function findLastWeak();

    /**
     * @param \DateTimeInterface $period
     *
     * @return TaskLog[]
     */
    public function findByPeriod(\DateTimeInterface $period);

    /**
     * Persist and flush task log.
     *
     * @param TaskLog $taskLog
     */
    public function save(TaskLog $taskLog);
}