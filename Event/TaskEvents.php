<?php

namespace BobrD\SchedulerBundle\Event;

final class TaskEvents
{
    const TASK_STARTED = 'task_started';

    const TASK_FINISHED = 'task_finished';

    private function __construct()
    {
    }
}
