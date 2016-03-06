<?php

namespace BobrD\SchedulerBundle\Services\Scheduler;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface TaskInterface
{
    /**
     * @return CronTime
     */
    public function getCronTime();

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getDescription();
}
