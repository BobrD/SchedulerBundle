<?php

namespace BobrD\SchedulerBundle\Exception;

use BobrD\SchedulerBundle\Services\Scheduler\Exception\SchedulerException;
use Ramsey\Uuid\UuidInterface;

class SchedulerBundleException extends SchedulerException
{
    /**
     * @param UuidInterface $uuid
     * 
     * @return static
     */
    public static function taskLogWithUuidNotFound(UuidInterface $uuid)
    {
        return new static(sprintf('Task log with uuid %s not found.', $uuid->toString()));
    }

    /**
     * @param string $name
     * 
     * @return static
     */
    public static function taskWithNameNotFound($name)
    {
        return new static(sprintf('Task with name %s not found.', $name));
    }
}
