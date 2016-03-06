<?php

namespace BobrD\SchedulerBundle\Services;

use BobrD\SchedulerBundle\Services\Scheduler\TaskInterface;
use BobrD\SchedulerBundle\Services\Scheduler\TaskProviderInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;

class SymfonyCommandProvider implements TaskProviderInterface
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * {@inheritdoc}
     */
    public function getTasks()
    {
        return array_filter($this->application->all(), function (Command $command) {
            return $command instanceof TaskInterface;
        });
    }
}
