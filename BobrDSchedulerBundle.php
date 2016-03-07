<?php

namespace BobrD\SchedulerBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\DBAL\Types\Type;
use Ramsey\Uuid\Doctrine\UuidType;
use BobrD\SchedulerBundle\DependencyInjection\Compiler\AddTaskProviderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BobrDSchedulerBundle extends Bundle
{
    public function __construct()
    {
        if (!Type::hasType('uuid')) {
            Type::addType('uuid', UuidType::class);
        }
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddTaskProviderPass());

        $this->addRegisterMappingsPass($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function addRegisterMappingsPass(ContainerBuilder $container)
    {
        $mappings = [
            realpath(__DIR__.'/Resources/config/doctrine/model') => 'BobrD\SchedulerBundle\Model',
        ];

        if (class_exists('Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass')) {
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings));
        }
    }
}
