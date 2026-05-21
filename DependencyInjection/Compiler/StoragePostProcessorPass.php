<?php declare(strict_types=1);

namespace C201\MediaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Adds tagged c201_media.transformer services to media
 */
class StoragePostProcessorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container) : void
    {
        $definition = $container->getDefinition('c201_media.storage.post_processor_pool');

        foreach ($container->findTaggedServiceIds('c201_media.storage.post_processor') as $id => $attributes) {
            $definition->addMethodCall('addPostProcessor', [$attributes[0]['alias'], new Reference($id)]);
        }
    }
}
