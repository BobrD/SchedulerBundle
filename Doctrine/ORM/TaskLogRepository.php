<?php

namespace BobrD\SchedulerBundle\Doctrine\ORM;

use BobrD\SchedulerBundle\Exception\SchedulerBundleException;
use BobrD\SchedulerBundle\Repository\TaskLogRepositoryInterface;
use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\UuidInterface;
use BobrD\SchedulerBundle\Model\TaskLog;

class TaskLogRepository extends EntityRepository implements TaskLogRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getByUuid(UuidInterface $uuid)
    {
        $task = parent::find($uuid->toString());

        if (null === $task) {
            throw SchedulerBundleException::taskLogWithUuidNotFound($uuid);
        }

        return $task;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findLastHour()
    {
        return $this->findByPeriod(new \DateTime('-1 hour'));
    }

    /**
     * {@inheritdoc}
     */
    public function findLastDay()
    {
        return $this->findByPeriod(new \DateTime('-1 day'));
    }

    /**
     * {@inheritdoc}
     */
    public function findLastWeak()
    {
        return $this->findByPeriod(new \DateTime('-1 weak'));
    }

    /**
     * {@inheritdoc}
     */
    public function findByPeriod(\DateTimeInterface $period)
    {
        $qb = $this->createQueryBuilder('task');

        $qb
            ->andWhere(
                $qb->expr()->gte('task.startedAt', ':period')
            )
            ->setParameter('period', $period)
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function save(TaskLog $taskLog)
    {
        $this->_em->persist($taskLog);
        $this->_em->flush($taskLog);
    }
}
