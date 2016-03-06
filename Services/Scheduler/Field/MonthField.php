<?php

namespace BobrD\SchedulerBundle\Services\Scheduler\Field;

class MonthField extends AbstractField
{
    const MONTH_CODES_PATTERN = '(?:JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)';

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
        return 12;
    }

    public function getTime(\DateTimeInterface $dateTime)
    {
        return intval($dateTime->format('m'));
    }
}
