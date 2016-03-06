<?php

namespace BobrD\SchedulerBundle\Services\Scheduler\Field;

class MinuteField extends AbstractField
{
    /**
     * @param \DateTimeInterface $dateTime
     *
     * @return mixed
     */
    public function getTime(\DateTimeInterface $dateTime)
    {
        return intval($dateTime->format('i'));
    }

    /**
     * @return int
     */
    protected function getLowerBoundary()
    {
        return 0;
    }

    /**
     * @return int
     */
    protected function getUpperBoundary()
    {
        return 59;
    }
}
