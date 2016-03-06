<?php

namespace spec\BobrD\SchedulerBundle\Entity;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Ramsey\Uuid\UuidInterface;

/**
 * @mixin \BobrD\SchedulerBundle\Model\TaskLog
 */
class TaskLogSpec extends ObjectBehavior
{
    function let(UuidInterface $uuid)
    {
        $this->beConstructedWith($uuid, 'task_name', 'task_description', '* * * * *');
    }

    function it_get_constructor_arguments(UuidInterface $uuid)
    {
        $this->getUuid()->shouldBe($uuid);
        $this->getName()->shouldBe('task_name');
        $this->getDescription()->shouldBe('task_description');
        $this->getCronTime()->shouldBe('* * * * *');
    }

    function it_started_when_is_new()
    {
        $this->isStarted()->shouldBe(true);
    }

    function it_has_started_time()
    {
        $this->getStartedAt()->shouldBeAnInstanceOf(\DateTime::class);
    }
    
    function it_return_null_if_task_not_finished()
    {
        $this->getFinishedAt()->shouldBeNull();
    }

    function it_return_null_if_is_not_finished()
    {
        $this->isFinishedSuccessful()->shouldBeNull();
        $this->getOutput()->shouldBeNull();
        $this->getStackTrace()->shouldBeNull();
        $this->getExecutedTime()->shouldBeNull();
    }
    
    function it_can_be_marked_as_finished()
    {
        $this->markAsFinished(true, 'some_output');

        $this->isStarted()->shouldBe(false);
        $this->isFinished()->shouldBe(true);
        $this->isFinishedSuccessful()->shouldBe(true);
        $this->getOutput()->shouldBe('some_output');
        $this->getStackTrace()->shouldBeNull();
        $this->getExecutedTime()->shouldBeAnInstanceOf(\DateInterval::class);
        $this->getFinishedAt()->shouldBeAnInstanceOf(\DateTime::class);
    }
    
    function it_can_return_stackTrace_if_failed()
    {
        $this->markAsFinished(false, 'some_output', 'some_trace');
        
        $this->getStackTrace()->shouldBe('some_trace');
    }
}
