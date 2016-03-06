<?php

namespace BobrD\SchedulerBundle\Services\Scheduler;

/**
 * Предоставляет возможность запускать задачи из разных источников.
 */
interface TaskProviderInterface
{
    /**
     * @return TaskInterface[]
     */
    public function getTasks();
}
