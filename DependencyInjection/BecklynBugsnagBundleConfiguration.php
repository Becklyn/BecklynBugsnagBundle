<?php

namespace Becklyn\BugsnagBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;


/**
 *
 */
class BecklynBugsnagBundleConfiguration implements ConfigurationInterface
{
    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder ()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root("becklyn_bugsnag");

        $root
            ->children()
                ->scalarNode('api_key')
                    ->defaultNull()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
