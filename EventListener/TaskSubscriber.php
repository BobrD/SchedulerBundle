<?php

namespace BobrD\SchedulerBundle\EventListener;

use BobrD\SchedulerBundle\Model\TaskLog;
use BobrD\SchedulerBundle\Event\TaskEvents;
use BobrD\SchedulerBundle\Event\TaskFinishedEvent;
use BobrD\SchedulerBundle\Event\TaskStartedEvent;
use BobrD\SchedulerBundle\Doctrine\ORM\TaskLogRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TaskSubscriber implements EventSubscriberInterface
{
    /**
     * @var TaskLogRepository
     */
    private $taskRepository;

    /**
     * @param TaskLogRepository $taskRepository
     */
    public function __construct(TaskLogRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            TaskEvents::TASK_STARTED => ['taskStarted'],
            TaskEvents::TASK_FINISHED => ['taskFinished'],
        ];
    }

    /**
     * @param TaskStartedEvent $event
     */
    public function taskStarted(TaskStartedEvent $event)
    {
        $task = $event->getTask();

        $taskEntity = new TaskLog(
            $event->getUuid(),
            $task->getName(),
            $task->getDescription(),
            $task->getCronTime()->toString()
        );

        $this->taskRepository->save($taskEntity);
    }

    /**
     * @param TaskFinishedEvent $event
     */
    public function taskFinished(TaskFinishedEvent $event)
    {
        $task = $this->taskRepository->getByUuid($event->getUuid());

        $task->markAsFinished(
            $event->isFinishedSuccessful(),
            $event->getOutput(),
            $event->getExceptionTrace()
        );

        $this->taskRepository->save($task);
    }
}
