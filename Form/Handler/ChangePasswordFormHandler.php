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
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ChangePasswordFormHandler extends FOSUserChangePasswordFormHandler
{
	/**
	 *
	 * @var \DateTime
	 */
	protected $credentialsExpireAt;

	/**
	 * Constructor
	 * 
	 * @param FormInterface        $form
	 * @param Request              $request
	 * @param UserManagerInterface $userManager
	 * @param string               $credentialsExpireAt
	 */
	public function __construct(FormInterface $form, Request $request, UserManagerInterface $userManager, $credentialsExpireAt)
	{
		parent::__construct($form, $request, $userManager);

		if ($credentialsExpireAt) {
			$credentialsExpireAt = new \DateTime($credentialsExpireAt);
		}

		$this->credentialsExpireAt = $credentialsExpireAt;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function onSuccess(UserInterface $user)
	{
		parent::onSuccess($user);

		// Cogitoweb: remove credentials expired flag and next expiry date
		$user->setCredentialsExpired(false);
		$user->setCredentialsExpireAt($this->credentialsExpireAt);
        $this->userManager->updateUser($user);
	}
}
