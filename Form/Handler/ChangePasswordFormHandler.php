<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cogitoweb\UserBundle\Form\Handler;

use FOS\UserBundle\Form\Handler\ChangePasswordFormHandler as FOSUserChangePasswordFormHandler;
use FOS\UserBundle\Model\UserInterface;

class ChangePasswordFormHandler extends FOSUserChangePasswordFormHandler
{
	/**
	 * {@inheritdoc}
	 */
	protected function onSuccess(UserInterface $user)
	{
		parent::onSuccess($user);

		// Cogitoweb: remove credentials expired flag and next expiry date
		$user->setCredentialsExpired(false);
		$user->setCredentialsExpireAt(null);
        $this->userManager->updateUser($user);
	}
}
