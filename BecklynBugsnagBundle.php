<?php

namespace Becklyn\BugsnagBundle;

use Becklyn\BugsnagBundle\DependencyInjection\BecklynBugsnagExtension;
use Becklyn\BugsnagBundle\DependencyInjection\MonitoringCompilerPass;
use Becklyn\BugsnagBundle\DependencyInjection\ProcessedConfiguration;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;


/**
 *
 */
class BecklynBugsnagBundle extends Bundle
{
    /**
     * @var ProcessedConfiguration
     */
    private $processedConfiguration;


    /**
     * @inheritDoc
     */
    public function __construct ()
    {
        $this->processedConfiguration = new ProcessedConfiguration();
    }



    /**
     * @inheritDoc
     */
    public function build (ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new MonitoringCompilerPass($this->processedConfiguration));
    }



    /**
     * @inheritDoc
     */
    public function getContainerExtension ()
    {
        if (null === $this->extension)
        {
            $this->extension = new BecklynBugsnagExtension($this->processedConfiguration);
        }

        return $this->extension;
    }
}
