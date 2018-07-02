<?php

namespace C201\MediaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Adds tagged c201_media.storage_backend services to pool of storage backends
 */
class StorageBackendPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('c201_media.storage.storage_backend_pool');

        foreach ($container->findTaggedServiceIds('c201_media.storage_backend') as $id => $attributes) {
            $definition->addMethodCall('addStorageBackend', [$attributes[0]['alias'], new Reference($id)]);
        }
    }
}
