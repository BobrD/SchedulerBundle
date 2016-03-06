<?php

namespace BobrD\SchedulerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunSchedulerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('scheduler:run')
            ->setDescription('Run schedule.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('scheduler')->run();
    }
}
