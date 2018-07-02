<?php

namespace C201\MediaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Adds tagged c201_media.transformer services to media
 */
class StoragePostProcessorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('c201_media.storage.post_processor_pool');

        foreach ($container->findTaggedServiceIds('c201_media.storage.post_processor') as $id => $attributes) {
            $definition->addMethodCall('addPostProcessor', [$attributes[0]['alias'], new Reference($id)]);
        }
    }
}
