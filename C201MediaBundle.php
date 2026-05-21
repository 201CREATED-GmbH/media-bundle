<?php declare(strict_types=1);

namespace C201\MediaBundle;

use C201\MediaBundle\DependencyInjection\Compiler\StorageBackendPass;
use C201\MediaBundle\DependencyInjection\Compiler\StoragePostProcessorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class C201MediaBundle extends Bundle
{
    public function build (ContainerBuilder $container) : void
    {
        parent::build($container);

        $container->addCompilerPass(new StorageBackendPass());
        $container->addCompilerPass(new StoragePostProcessorPass());
    }
}
