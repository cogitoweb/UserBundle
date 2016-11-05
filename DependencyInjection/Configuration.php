<?php

namespace Cogitoweb\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('cogitoweb_user');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

		$rootNode
			->children()
				->arrayNode('change_password')
				->addDefaultsIfNotSet()
					->children()
					->scalarNode('credentials_expire_at')
						->info('Format suitable for PHP DateTime() class.')
						->defaultNull()
						->validate()
							->ifTrue($this->DateTimeConstraint())
							->thenInvalid('Format not suitable for PHP DateTime class.')
						->end()
					->end()
				->end()
			->end()
		;

        return $treeBuilder;
    }

	/**
	 * DateTime constraint
	 * 
	 * @return boolean Falce if value is accepted as argument by PHP DateTime constructor.
	 */
	protected function DateTimeConstraint()
	{
		return function ($value) {
			if ($value) {
				try {
					new \DateTime($value);
				} catch (\Exception $ex) {
					return true;
				}
			}

			return false;
		};
	}
}
