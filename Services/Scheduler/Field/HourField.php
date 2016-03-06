<?php

namespace BobrD\SchedulerBundle\Services\Scheduler\Field;

class HourField extends AbstractField
{
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
        return 23;
    }

    public function getTime(\DateTimeInterface $dateTime)
    {
        return intval($dateTime->format('H'));
    }
}
