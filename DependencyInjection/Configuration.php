<?php

namespace Fkr\CssURLRewriteBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fkr_css_url_rewrite');

        $rootNode
            ->children()
                ->scalarNode('rewrite_only_if_file_exists')->defaultValue(true)->end()
                ->scalarNode('clear_urls')->defaultValue(true)->end()
            ->end();

        return $treeBuilder;
    }
}
