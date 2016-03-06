<?php

namespace BobrD\SchedulerBundle\Services\Scheduler\Field;

abstract class AbstractField
{
    const TIME_PATTERN = '(?:\d+)';

    const FIELD_ALWAYS = 'always';

    const FIELD_EVERY = 'every';

    const FIELD_SPECIFIC = 'specific';

    const FIELD_RANGE = 'range';

    const FIELD_COMMA = 'comma';

    const FIELD_COMMA_EVERY = 'commaEvery';

    const FIELD_RANGE_EVERY = 'rangeEvery';

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
        $this->field = $field;
        $this->fieldType = $this->getFieldType($field);

        switch ($this->fieldType) {
            case self::FIELD_ALWAYS:
                break;
            case self::FIELD_EVERY:
                $this->checkEvery($field);
                break;
            case self::FIELD_SPECIFIC:
                $this->checkSpecific($field);
                break;
            case self::FIELD_RANGE:
                $this->checkRange($field);
                break;
            case self::FIELD_COMMA:
                $this->checkComma($field);
                break;
            case self::FIELD_COMMA_EVERY:
                $this->checkCommaEvery($field);
                break;
            case self::FIELD_RANGE_EVERY:
                $this->checkRangeEvery($field);
                break;
            default:
                break;
        }
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

        $result = false;

        switch ($this->fieldType) {
            case self::FIELD_ALWAYS:
                $result = $this->matchAlways();
                break;
            case self::FIELD_EVERY:
                $result = $this->matchEvery($time, $this->field);
                break;
            case self::FIELD_SPECIFIC:
                $result = $this->matchSpecific($time, $this->field);
                break;
            case self::FIELD_RANGE:
                $result = $this->matchRange($time, $this->field);
                break;
            case self::FIELD_COMMA:
                $result = $this->matchComma($time, $this->field);
                break;
            case self::FIELD_COMMA_EVERY:
                $result = $this->matchCommaEvery($time, $this->field);
                break;
            case self::FIELD_RANGE_EVERY:
                $result = $this->matchRangeEvery($time, $this->field);
                break;
            default:
                break;
        }

        return $result;
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
     * @param mixed $value
     *
     * @throws \InvalidArgumentException
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
     *
     * @throws \InvalidArgumentException
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
     * @param string $field
     */
    private function checkEvery($field)
    {
        $every = intval(explode('/', $field)[1]);
        $this->checkInterval($every);
    }

    /**
     * @param string $field
     *
     * @throws \InvalidArgumentException
     */
    private function checkSpecific($field)
    {
        if ($field < $this->getLowerBoundary() || $field > $this->getUpperBoundary()) {
            throw new \InvalidArgumentException();
        }
    }

    /**
     * @param string $field ex: 2,4,5
     *
     * @throws \InvalidArgumentException
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
     *
     * @throws \InvalidArgumentException
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
     *
     * @throws \InvalidArgumentException
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
     *
     * @throws \InvalidArgumentException
     */
    private function getFieldType($field)
    {
        $cronPattern = $this->createCronPattern();

        if (!preg_match($cronPattern, $field, $matches)) {
            throw new \InvalidArgumentException();
        }

        $availableFieldType = [
            self::FIELD_ALWAYS,
            self::FIELD_EVERY,
            self::FIELD_SPECIFIC,
            self::FIELD_RANGE,
            self::FIELD_COMMA,
            self::FIELD_COMMA_EVERY,
            self::FIELD_RANGE_EVERY,
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
                (?<'.self::FIELD_ALWAYS.'>^\*$)|
                (?<'.self::FIELD_EVERY.'>^\*\/'.$fullTimePattern.'+$)|
                (?<'.self::FIELD_SPECIFIC.'>^'.$fullTimePattern.'+$)|
                (?<'.self::FIELD_RANGE.'>^'.$fullTimePattern.'-'.$fullTimePattern.'$)|
                (?<'.self::FIELD_COMMA.'>^'.$fullTimePattern.'(?:,\d+)+$)|
                (?<'.self::FIELD_COMMA_EVERY.'>^'.$fullTimePattern.'(?:,'.$fullTimePattern.')+\/\d+$)|
                (?<'.self::FIELD_RANGE_EVERY.'>^'.$fullTimePattern.'-'.$fullTimePattern.'\/\d+$)
            /xi
            ';
    }
}
