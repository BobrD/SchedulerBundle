<?php

namespace spec\BobrD\SchedulerBundle\Services\Scheduler;

use BobrD\SchedulerBundle\Services\Scheduler\CronTime;
use BobrD\SchedulerBundle\Services\Scheduler\Exception\SchedulerException;
use PhpSpec\ObjectBehavior;

/**
 * @mixin CronTime
 */
class CronTimeSpec extends ObjectBehavior
{
    function it_throw_exception_if_pattern_is_invalid()
    {
        $this->beConstructedWith('* * *');

        $this->shouldThrow(SchedulerException::class)->duringInstantiation();
    }

    /**
     * @dataProvider timeDataSet
     */
    function it_is_match_pattern($date, $pattern, $expected)
    {
        $this->beConstructedWith($pattern);

        $date = \DateTime::createFromFormat('Y-m-d H:i', $date);

        $this->isMatch($date)->shouldBe($expected);
    }

    function timeDataSet()
    {
        return [
            ['2015-12-30 22:50', '* * * * *', true],
            ['2015-12-30 22:50', '*/2 * * * *', true],
            ['2015-12-30 22:50', '*/3 * * * *', false],
            ['2015-12-30 22:50', '* */22 * * *', true],
            ['2015-12-30 22:50', '* */2 * * *', true],
            ['2015-12-30 22:50', '* */3 * * *', false],
            ['2015-12-30 22:50', '50 */3 * * *', false],
            ['2015-12-30 22:50', '5,10,15,50 * * * *', true],
        ];
    }
}
