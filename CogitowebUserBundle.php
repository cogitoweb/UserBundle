<?php

namespace Cogitoweb\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CogitowebUserBundle extends Bundle
{
	/**
	 * {@inheritdoc}
	 */
	public function getParent()
	{
		return 'SonataUserBundle';
	}
}