<?php

namespace BobrD\SchedulerBundle\Command;

use BobrD\SchedulerBundle\Services\Scheduler\TaskInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SummaryCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('scheduler:summary')
            ->setDescription('Show list of cron tasks.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $tasks = $this->getContainer()->get('scheduler')->getTasks();

        $io->table([
            'Name',
            'Cron pattern',
        ], array_map(function (TaskInterface $task) {
            return [
                $task->getCronTime()->toString(),
                $task->getName(),
            ];
        }, $tasks));
    }
}
