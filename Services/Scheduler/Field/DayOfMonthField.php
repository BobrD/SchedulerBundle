<?php

namespace BobrD\SchedulerBundle\Services\Scheduler\Field;

class DayOfMonthField extends AbstractField
{
    /**
     * @return int
     */
    protected function getLowerBoundary()
    {
        return 1;
    }

    /**
     * @return int
     */
    protected function getUpperBoundary()
    {
        return 31;
    }

    /**
     * @param \DateTimeInterface $dateTime
     *
     * @return int
     */
    public function getTime(\DateTimeInterface $dateTime)
    {
        return intval($dateTime->format('d'));
    }
}
