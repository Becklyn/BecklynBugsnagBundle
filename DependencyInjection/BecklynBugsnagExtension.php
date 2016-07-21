<?php

namespace Becklyn\BugsnagBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


/**
 *
 */
class BecklynBugsnagExtension extends Extension
{
    /**
     * @var ProcessedConfiguration
     */
    private $processedConfiguration;


    /**
     * @inheritDoc
     */
    public function __construct (ProcessedConfiguration $processedConfiguration)
    {
        $this->processedConfiguration = $processedConfiguration;
    }



    /**
     * @inheritdoc
     */
    public function load (array $configs, ContainerBuilder $container)
    {
        // load services.yml
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new BecklynBugsnagBundleConfiguration();
        $processed = $this->processConfiguration($configuration, $configs);

        // store processed configuration
        $this->processedConfiguration->setConfiguration($processed);
    }
}
