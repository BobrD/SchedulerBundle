<?php

namespace BobrD\SchedulerBundle\Services\Scheduler\Field;

abstract class AbstractField
{
    const TIME_PATTERN = '(?:\d+)';

    const ALWAYS_FIELD = 'always';

    const EVERY_FIELD = 'every';

    const SPECIFIC_FIELD = 'specific';

    const RANGE_FIELD = 'range';

    const COMMA_FIELD = 'comma';

    const COMMA_EVERY_FIELD = 'commaEvery';

    const RANGE_EVERY_FIELD = 'rangeEvery';

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $fieldType;

    /**
     * @param string $field
     */
    public function __construct($field = '*')
    {
        $this->field($field);
    }

    /**
     * @param string $field
     */
    public function field($field)
    {
        $type = $this->getFieldType($field);

        $this->{'check'.ucfirst($type)}($field);

        $this->fieldType = $type;
        $this->field = $field;
    }

    /**
     * @param \DateTimeInterface $dateTime
     *
     * @return mixed
     */
    abstract public function getTime(\DateTimeInterface $dateTime);

    /**
     * @param \DateTimeInterface $dateTime
     *
     * @return bool
     */
    public function isMatch(\DateTimeInterface $dateTime)
    {
        $time = $this->getTime($dateTime);

        return $this->{'match'.ucfirst($this->fieldType)}($time, $this->field);
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->field;
    }

    /**
     * @return int
     */
    abstract protected function getLowerBoundary();

    /**
     * @return int
     */
    abstract protected function getUpperBoundary();

    /**
     * @return array
     */
    protected function getTimePatterns()
    {
        return [self::TIME_PATTERN];
    }

    /**
     * @return bool
     */
    private function checkAlways()
    {
        return true;
    }

    /**
     * @param $value
     */
    private function checkInterval($value)
    {
        $value = intval($value);

        if ($value < $this->getLowerBoundary() || $value > $this->getUpperBoundary()) {
            throw new \InvalidArgumentException();
        }
    }

    /**
     * @param string $field ex: 1-10
     */
    private function checkRange($field)
    {
        if (!strstr($field, '-')) {
            return;
        }

        $range = explode('-', $field);
        $begin = $range[0];
        $end = $range[1];
        $this->checkInterval($begin);
        $this->checkInterval($end);

        if ($begin > $end) {
            throw new \InvalidArgumentException();
        }
    }

    /**
     * @param $minute
     */
    private function checkEvery($minute)
    {
        $every = intval(explode('/', $minute)[1]);
        $this->checkInterval($every);
    }

    /**
     * @param string $field ex: 2,4,5
     */
    private function checkComma($field)
    {
        $times = explode(',', $field);

        $maxItems = $this->getUpperBoundary() - $this->getLowerBoundary();
        $totalItems = count($times);

        if ($totalItems > $maxItems || count(array_unique($times)) < $totalItems) {
            throw new \InvalidArgumentException();
        }

        foreach ($times as $time) {
            $this->checkInterval($time);
        }
    }

    /**
     * @param string $field ex: 2,4,5/2
     */
    private function checkCommaEvery($field)
    {
        if (!strstr($field, '/')) {
            return;
        }

        $parts = explode('/', $field);
        $comma = $parts[0];
        $every = $parts[1];
        $this->checkComma($comma);
        $this->checkEvery($every);
    }

    /**
     * @param string $field ex 1-9/2
     */
    private function checkRangeEvery($field)
    {
        if (!strstr($field, '/')) {
            return;
        }

        $parts = explode('/', $field);
        $range = $parts[0];
        $every = $parts[1];
        $this->checkRange($range);
        $this->checkEvery($every);
    }

    /**
     * @param $time
     * @param $field
     *
     * @return bool
     */
    private function matchEvery($time, $field)
    {
        $field = intval(str_replace('*/', '', $field));

        if ($field === 0 && $field === $time) {
            return true;
        } elseif ($field === 0 && $field !== $time) {
            return false;
        } else {
            return $time % $field === 0;
        }
    }

    /**
     * @param $time
     * @param $field
     *
     * @return bool
     */
    private function matchRange($time, $field)
    {
        if (!strstr($field, '-')) {
            return false;
        }

        $range = explode('-', $field);
        $begin = $range[0];
        $end = $range[1];

        return $time >= $begin && $time <= $end;
    }

    /**
     * @param $time
     * @param $field
     *
     * @return bool
     */
    private function matchComma($time, $field)
    {
        if (!strstr($field, ',')) {
            return false;
        }

        $times = explode(',', $field);

        return in_array($time, $times);
    }

    /**
     * @param $time
     * @param $field
     *
     * @return bool
     */
    private function matchCommaEvery($time, $field)
    {
        if (!strstr($field, '/')) {
            return false;
        }

        $parts = explode('/', $field);
        $comma = $parts[0];
        $every = '*/'.$parts[1];

        return $this->matchComma($time, $comma) && $this->matchEvery($time, $every);
    }

    /**
     * @param $time
     * @param $field
     *
     * @return bool
     */
    private function matchRangeEvery($time, $field)
    {
        if (!strstr($field, '/')) {
            return false;
        }

        $parts = explode('/', $field);
        $range = $parts[0];
        $every = '*/'.$parts[1];

        return $this->matchRange($time, $range) && $this->matchEvery($time, $every);
    }

    /**
     * @return bool
     */
    private function matchAlways()
    {
        return true;
    }

    /**
     * @param $time
     * @param $field
     *
     * @return bool
     */
    private function matchSpecific($time, $field)
    {
        return $time === $field;
    }

    /**
     * @param $field
     *
     * @return string one of field type
     */
    private function getFieldType($field)
    {
        $cronPattern = $this->createCronPattern();

        if (!preg_match($cronPattern, $field, $matches)) {
            throw new \InvalidArgumentException();
        }

        $availableFieldType = [
            self::ALWAYS_FIELD,
            self::EVERY_FIELD,
            self::RANGE_FIELD,
            self::COMMA_FIELD,
            self::COMMA_EVERY_FIELD,
            self::RANGE_EVERY_FIELD,
        ];

        foreach ($availableFieldType as $fieldTypeName) {
            if (!isset($matches[$fieldTypeName]) || !empty($matches[$fieldTypeName])) {
                return $fieldTypeName;
            }
        }

        throw new \InvalidArgumentException();
    }

    /**
     * @return string
     */
    private function createCronPattern()
    {
        $fullTimePattern = '(?:'.implode('|', $this->getTimePatterns()).')';

        return '
            /
                (?<'.self::ALWAYS_FIELD.'>^\*$)|
                (?<'.self::EVERY_FIELD.'>^\*\/'.$fullTimePattern.'+$)|
                (?<'.self::SPECIFIC_FIELD.'>^'.$fullTimePattern.'+$)|
                (?<'.self::RANGE_FIELD.'>^'.$fullTimePattern.'-'.$fullTimePattern.'$)|
                (?<'.self::COMMA_FIELD.'>^'.$fullTimePattern.'(?:,\d+)+$)|
                (?<'.self::COMMA_EVERY_FIELD.'>^'.$fullTimePattern.'(?:,'.$fullTimePattern.')+\/\d+$)|
                (?<'.self::RANGE_EVERY_FIELD.'>^'.$fullTimePattern.'-'.$fullTimePattern.'\/\d+$)
            /xi
            ';
    }
}
