<?php

namespace BobrD\SchedulerBundle\Services\Scheduler\Field;

class DayOfWeekField extends AbstractField
{
    const DAY_OF_WEEK_CODES_PATTERN = '(?:SUN|MON|TUE|WED|THU|FRI|SAT)';

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
        return 6;
    }

    public function getTime(\DateTimeInterface $dateTime)
    {
        return intval($dateTime->format('w'));
    }
}
