<?php

namespace BobrD\SchedulerBundle\Services\Scheduler\Exception;

class SchedulerException extends \Exception
{
    /**
     * @param string $pattern
     * 
     * @return static
     */
    public static function invalidCronTimePattern($pattern)
    {
        return new static(sprintf('Invalid cron time pattern: "%s".', $pattern));
    }
}
