<?php

namespace BobrD\SchedulerBundle\Services\Scheduler;

use BobrD\SchedulerBundle\Services\Scheduler\Field\AbstractField;
use BobrD\SchedulerBundle\Services\Scheduler\Field\DayOfMonthField;
use BobrD\SchedulerBundle\Services\Scheduler\Field\DayOfWeekField;
use BobrD\SchedulerBundle\Services\Scheduler\Field\HourField;
use BobrD\SchedulerBundle\Services\Scheduler\Field\MinuteField;
use BobrD\SchedulerBundle\Services\Scheduler\Field\MonthField;
use BobrD\SchedulerBundle\Services\Scheduler\Exception\SchedulerException;

class CronTime
{
    /**
     * @var MinuteField
     */
    private $minute;

    /**
     * @var HourField
     */
    private $hour;

    /**
     * @var DayOfMonthField
     */
    private $dayOfMonth;

    /**
     * @var MonthField
     */
    private $month;

    /**
     * @var DayOfWeekField
     */
    private $dayOfWeek;

    /**
     * @param string $pattern
     */
    public function __construct($pattern = '* * * * *')
    {
        $this->cron($pattern);
    }

    /**
     * Create from cron pattern.
     *
     * @param string $pattern
     *
     * @throws SchedulerException
     */
    public function cron($pattern)
    {
        $fields = explode(' ', $pattern);

        if (5 !== count($fields)) {
            throw SchedulerException::invalidCronTimePattern($pattern);
        }

        $this->minute = new MinuteField($fields[0]);
        $this->hour = new HourField($fields[1]);
        $this->dayOfMonth = new DayOfMonthField($fields[2]);
        $this->month = new MonthField($fields[3]);
        $this->dayOfWeek = new DayOfWeekField($fields[4]);
    }

    /**
     * @param string $minute
     *
     * @return $this
     */
    public function minute($minute)
    {
        $this->minute = new MinuteField($minute);

        return $this;
    }

    /**
     * @param string $hour
     *
     * @return $this
     */
    public function hour($hour)
    {
        $this->hour = new HourField($hour);

        return $this;
    }

    /**
     * @param string $dayOfMonth
     *
     * @return $this
     */
    public function dayOfMonth($dayOfMonth)
    {
        $this->dayOfMonth = new DayOfMonthField($dayOfMonth);

        return $this;
    }

    /**
     * @param string $month
     *
     * @return $this
     */
    public function month($month)
    {
        $this->month = new MonthField($month);

        return $this;
    }

    /**
     * @param string $dayOfWeek
     *
     * @return $this
     */
    public function dayOfWeek($dayOfWeek)
    {
        $this->dayOfWeek = new DayOfWeekField($dayOfWeek);

        return $this;
    }

    /**
     * Check that pattern match date time.
     *
     * @param \DateTimeInterface|null $dateTime
     *
     * @return bool
     */
    public function isMatch(\DateTimeInterface $dateTime = null)
    {
        $dateTime = $dateTime ?: new \DateTime();

        foreach ($this->getFields() as $field) {
            if (false === $field->isMatch($dateTime)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function toString()
    {
        $pieces = [];
        foreach ($this->getFields() as $field) {
            $pieces[] = $field->toString();
        }

        return implode(' ', $pieces);
    }

    /**
     * @return array|AbstractField[]
     */
    private function getFields()
    {
        return [
            $this->minute,
            $this->hour,
            $this->dayOfMonth,
            $this->month,
            $this->dayOfWeek,
        ];
    }
}
