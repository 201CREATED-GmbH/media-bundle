<?php

namespace C201\MediaBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

class C201MediaExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('c201_media.base_upload_path', $config['base_upload_path']);
        $container->setParameter('c201_media.objects', $config['objects']);


        $container->setParameter('twig.form.resources', array_merge(
            $container->getParameter('twig.form.resources'),
            ['@C201Media/Form/fields.html.twig']
        ));

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('config.xml');
        $loader->load('model.xml');

        // load liip_imagine specific configuration, when it is registered in the kernel
        $bundles = $container->getParameter('kernel.bundles');
        if (isset($bundles['JMSSerializerBundle'])) {
            $loader->load('jms_serializer.xml');
        }
    }
}
