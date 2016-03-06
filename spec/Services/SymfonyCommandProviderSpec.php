<?php

namespace spec\BobrD\SchedulerBundle\Services;

use BobrD\SchedulerBundle\Services\Scheduler\TaskInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @mixin \BobrD\SchedulerBundle\Services\SymfonyCommandProvider
 */
class SymfonyCommandProviderSpec extends ObjectBehavior
{
    function let(Application $application)
    {
        $this->beConstructedWith($application);
    }

    function it_get_all_tasks(Application $application, SomeTask $task)
    {
        $application->all()->willReturn([$task, $task]);
        
        $this->getTasks()->shouldBe([$task, $task]);
    }
}

class SomeTask extends Command implements TaskInterface
{
    public function execute(InputInterface $input, OutputInterface $output){}

    public function getCronTime(){}
}