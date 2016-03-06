<?php

namespace BobrD\SchedulerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AddTaskProviderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('scheduler.scheduler')) {
            return;
        }

        $definition = $container->findDefinition('scheduler.scheduler');

        $taggedServices = $container->findTaggedServiceIds('scheduler.task_provider');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'addProvider',
                [new Reference($id)]
            );
        }
    }
}
