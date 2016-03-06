<?php

namespace BobrD\SchedulerBundle\Command;

use BobrD\SchedulerBundle\Model\TaskLog;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ExecuteLogCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('scheduler:log')
            ->setDescription('Show execution log.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tasks = $this->getContainer()->get('scheduler.task_log_repository')->findAll();

        $io = new SymfonyStyle($input, $output);

        $io->table([
            'Started',
            'Status',
            'Name',
            'Description',
            'Cron pattern',
            'Started at',
            'Finished at',
            'Executed time',
            'Output',
            'StackTrace',
        ], array_map(function (TaskLog $task) {

            if ($task->isStarted()) {
                $statusLabel = 'running';
            } elseif ($task->isFinishedSuccessful()) {
                $statusLabel = 'success';
            } else {
                $statusLabel = 'fail';
            }

            return [
                $task->isStarted() ? 'Yes' : 'No',
                $statusLabel,
                $task->getName(),
                $task->getDescription(),
                $task->getCronTime(),
                $task->getStartedAt()->format('Y-m-d H:i:s'),
                $task->getFinishedAt() ? $task->getFinishedAt()->format('Y-m-d H:i:s') : '-//-',
                $task->getExecutedTime() ? $task->getExecutedTime()->format('%h:%i:%s') : '-//-',
                $task->getOutput(),
                $task->getStackTrace(),

            ];
        }, $tasks));
    }
}
