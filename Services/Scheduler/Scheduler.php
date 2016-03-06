<?php

namespace BobrD\SchedulerBundle\Services\Scheduler;

use BobrD\SchedulerBundle\Exception\SchedulerBundleException;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use BobrD\SchedulerBundle\Event\TaskEvents;
use BobrD\SchedulerBundle\Event\TaskFinishedEvent;
use BobrD\SchedulerBundle\Event\TaskStartedEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Scheduler
{
    /**
     * @var TaskProviderInterface[]
     */
    private $taskProviders = [];

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $runInSubProcess;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface $logger
     * @param bool $runInSubProcess
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger,
        $runInSubProcess = true
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
        $this->runInSubProcess = $runInSubProcess;
    }

    /**
     * @param TaskProviderInterface $taskProvider
     */
    public function addProvider(TaskProviderInterface $taskProvider)
    {
        $this->taskProviders[] = $taskProvider;
    }

    /**
     * Start cron tasks in sync mode.
     */
    public function run()
    {
        $now = new \DateTime();

        foreach ($this->getTasks() as $task) {
            if ($task->getCronTime()->isMatch($now)) {
                $this->doRun($task);
            }
        }
    }

    /**
     * @param string $name
     */
    public function runWithName($name)
    {
        if ($task = $this->getWithName($name)) {
            $this->doRun($task);
        }
    }

    /**
     * @return TaskInterface[]
     */
    public function getTasks()
    {
        $tasks = [];
        foreach ($this->taskProviders as $taskProvider) {
            $tasks = array_merge($tasks, $taskProvider->getTasks());
        }

        return $tasks;
    }

    /**
     * @param string $name
     *
     * @return TaskInterface
     * 
     * @throws SchedulerBundleException
     */
    public function getWithName($name)
    {
        foreach ($this->taskProviders as $taskProvider) {
            foreach ($taskProvider->getTasks() as $task) {
                if ($name === $task->getName()) {
                    return $task;
                }
            }
        }

        throw SchedulerBundleException::taskWithNameNotFound($name);
    }

    /**
     * @param TaskInterface $task
     */
    private function doRun(TaskInterface $task)
    {
        if ($this->runInSubProcess) {
            $this->runInSubProcess($task);
        } else {
            $this->execute($task);
        }
    }
    
    /**
     * @param TaskInterface $task
     */
    private function runInSubProcess(TaskInterface $task)
    {
        if (!function_exists('pcntl_fork')) {
            $this->logger->warning(
                'Task should be runned in sub process, but runned in main process, because function "pcntl_fork" don\'t exists.'
            );
           
            $this->execute($task);
        }

        $pid = pcntl_fork();

        if (-1 === $pid) {
            $this->logger->warning(
                'Task should be runned in sub process, but runned in main process, because cannot make fork.'
            );
                
            $this->execute($task);
        } elseif (0 === $pid) {
            $this->execute($task);
            exit;
        }
    }

    /**
     * @param TaskInterface $task
     */
    private function execute(TaskInterface $task)
    {
        $uuid = Uuid::uuid4();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $this->eventDispatcher->dispatch(TaskEvents::TASK_STARTED, new TaskStartedEvent($uuid, $task));

        try {
            $task->execute($input, $output);
        } catch (\Exception $e) {
            $this->logger->critical(sprintf('Error when execute cron task "%s".', $task->getName()), ['exception' => $e]);

            $this->eventDispatcher->dispatch(
                TaskEvents::TASK_FINISHED,
                new TaskFinishedEvent(
                    $uuid,
                    $task,
                    TaskFinishedEvent::STATUS_FAIL,
                    $output->fetch(),
                    $e->getTraceAsString()
                )
            );

            return;
        }

        $this->eventDispatcher->dispatch(
            TaskEvents::TASK_FINISHED,
            new TaskFinishedEvent(
                $uuid,
                $task,
                TaskFinishedEvent::STATUS_SUCCESS,
                $output->fetch()
            )
        );
    }
}
