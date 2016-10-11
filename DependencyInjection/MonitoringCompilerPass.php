<?php

namespace Becklyn\BugsnagBundle\DependencyInjection;

use Bugsnag\Client;
use Becklyn\BugsnagBundle\Monolog\Handler\BugsnagMonologHandler;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;


/**
 *
 */
class MonitoringCompilerPass implements CompilerPassInterface
{
    /**
     * The tag name from which the report transformers are collected
     */
    const REPORT_TRANSFORMER_TAG = "becklyn.bugsnag.report_transformer";

    /**
     * The service id on which the bugsnag handler is registered
     */
    const MONOLOG_HANDLER_SERVICE_ID = "becklyn.bugsnag.handler";

    /**
     * The service id of the javascript client
     */
    const JAVASCRIPT_CLIENT_SERVICE_ID = "becklyn.bugsnag.client.javascript";


    /**
     * @var ProcessedConfiguration
     */
    private $configuration;



    /**
     * @param ProcessedConfiguration $configuration
     */
    public function __construct (ProcessedConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }



    /**
     * @inheritdoc
     */
    public function process (ContainerBuilder $container)
    {
        if (null === $this->configuration->getApiKey())
        {
            return;
        }

        // set api key in JavaScript client
        $container->getDefinition(self::JAVASCRIPT_CLIENT_SERVICE_ID)
            ->replaceArgument(0, $this->configuration->getApiKey());

        // create PHP client
        $client = $this->createClientDefinition($container->getParameter("kernel.root_dir"));

        // create and register handlers
        $handler = $this->createMonologHandlerDefinition($client);
        $this->addLoggerReferenceToContainer($handler, $container);
        $this->collectReportTransformers($handler, $container);

        // register handlers in monolog
        $this->registerMonologHandlerInAllChannels($container);
    }



    /**
     * Creates the bugsnag client
     *
     * @param string $rootDir
     *
     * @return Definition
     */
    private function createClientDefinition ($rootDir)
    {
        $bugsnagClient = new Definition(Client::class, [
            $this->configuration->getApiKey(),
        ]);
        $bugsnag->setFilters(['clientIp']);
        $bugsnagClient->setFactory([Client::class, "make"]);
        $bugsnagClient->setPublic(false);

        // strip root dir from logs
        $rootDir = dirname($rootDir);
        $bugsnagClient->addMethodCall("setProjectRoot", [$rootDir]);
        $bugsnagClient->addMethodCall("setStripPath", [$rootDir]);

        return $bugsnagClient;
    }



    /**
     * Creates the monolog handler
     *
     * @param Definition $clientDefinition
     *
     * @return Definition
     */
    private function createMonologHandlerDefinition (Definition $clientDefinition)
    {
        return new Definition(BugsnagMonologHandler::class, [
            $clientDefinition
        ]);
    }



    /**
     * Adds the monolog handler to the service container
     *
     * @param Definition       $handlerDefinition
     * @param ContainerBuilder $container
     */
    private function addLoggerReferenceToContainer (Definition $handlerDefinition, ContainerBuilder $container)
    {
        // first wrap logger in FingersCrossedHandler
        $monitoringDefinition = new Definition(FingersCrossedHandler::class, [
            $handlerDefinition,
            Logger::WARNING
        ]);
        $monitoringDefinition->setPublic(false);

        $container->setDefinition(self::MONOLOG_HANDLER_SERVICE_ID, $monitoringDefinition);
    }



    /**
     * Collects all report transformers and registers them in the handler
     *
     * @param Definition       $handler
     * @param ContainerBuilder $container
     */
    private function collectReportTransformers (Definition $handler, ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds(self::REPORT_TRANSFORMER_TAG);

        foreach ($taggedServices as $serviceId => $tags)
        {
            $handler->addMethodCall("addReportTransformer", [new Reference($serviceId)]);
        }
    }



    /**
     * Registers the monolog handler in all channels
     *
     * @param ContainerBuilder $container
     */
    private function registerMonologHandlerInAllChannels (ContainerBuilder $container)
    {
        $monitoringReference = new Reference(self::MONOLOG_HANDLER_SERVICE_ID);

        // push reference as handler to every logger in the system
        foreach ($container->getDefinitions() as $key => $definition)
        {
            if (0 === strpos($key, "monolog.logger."))
            {
                $definition->addMethodCall("pushHandler", [$monitoringReference]);
            }
        }
    }
}
