<?php

namespace spec\BobrD\SchedulerBundle\Services\Scheduler;

use BobrD\SchedulerBundle\Event\TaskEvents;
use BobrD\SchedulerBundle\Event\TaskFinishedEvent;
use BobrD\SchedulerBundle\Event\TaskStartedEvent;
use BobrD\SchedulerBundle\Exception\SchedulerBundleException;
use BobrD\SchedulerBundle\Services\Scheduler\CronTime;
use BobrD\SchedulerBundle\Services\Scheduler\TaskInterface;
use BobrD\SchedulerBundle\Services\Scheduler\TaskProviderInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @mixin \BobrD\SchedulerBundle\Services\Scheduler\Scheduler
 */
class SchedulerSpec extends ObjectBehavior
{
    function let(EventDispatcherInterface $eventDispatcher, LoggerInterface $logger)
    {
        $this->beConstructedWith($eventDispatcher, $logger, false);
    }

    function it_run_tasks(
        TaskProviderInterface $taskProvider,
        SomeTask $task,
        CronTime $cronTime,
        EventDispatcherInterface $eventDispatcher
    ) {
        $taskProvider->getTasks()->willReturn([$task]);

        $task->getCronTime()->willReturn($cronTime);

        $cronTime->isMatch(Argument::type(\DateTime::class))->willReturn(true);

        $eventDispatcher->dispatch(TaskEvents::TASK_STARTED, Argument::type(TaskStartedEvent::class))->shouldBeCalled();

        $task->execute(Argument::type(ArrayInput::class), Argument::type(BufferedOutput::class))->shouldBeCalled();

        $eventDispatcher->dispatch(TaskEvents::TASK_FINISHED, Argument::type(TaskFinishedEvent::class))->shouldBeCalled();

        $this->addProvider($taskProvider);

        $this->run();
    }

    function it_run_task_with_name(
        TaskProviderInterface $taskProvider,
        SomeTask $task,
        CronTime $cronTime
    ) {
        $taskProvider->getTasks()->willReturn([$task]);

        $task->getName()->willReturn('task_name');

        $task->getCronTime()->willReturn($cronTime);

        $cronTime->isMatch(Argument::type(\DateTime::class))->willReturn(true);

        $task->execute(Argument::type(ArrayInput::class), Argument::type(BufferedOutput::class))->shouldBeCalled();

        $this->addProvider($taskProvider);

        $this->runWithName('task_name');
    }

    function it_get_all_tasks(
        TaskProviderInterface $taskProvider,
        SomeTask $task
    ) {
        $taskProvider->getTasks()->willReturn([$task, $task]);

        $this->addProvider($taskProvider);

        $this->getTasks()->shouldBe([$task, $task]);
    }

    function it_get_task_with_name(
        TaskProviderInterface $taskProvider,
        SomeTask $task
    ) {
        $taskProvider->getTasks()->willReturn([$task]);

        $task->getName()->willReturn('task_name');

        $this->addProvider($taskProvider);

        $this->getWithName('task_name')->shouldReturn($task);
    }

    function it_throw_exception_if_task_with_name_not_found(TaskProviderInterface $taskProvider)
    {
        $taskProvider->getTasks()->willReturn([]);

        $this->shouldThrow(SchedulerBundleException::class)->duringGetWithName('task_name');
    }
}

class SomeTask extends Command implements TaskInterface
{
    function execute(InputInterface $input, OutputInterface $output)
    {
    }

    function getCronTime()
    {
    }
}
