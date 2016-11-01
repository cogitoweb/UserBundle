<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cogitoweb\UserBundle\Security;

use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\User\UserChecker as SymfonyUserChecker;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Override Symfony UserChecker to ignore CredentialsExpiredException
 *
 * @author Daniele Artico <daniele.artico@cogitoweb.it>
 */
class UserChecker extends SymfonyUserChecker
{
	/**
	 * {@inheritdoc}
	 */
	public function checkPostAuth(UserInterface $user)
	{
		try {
			return parent::checkPostAuth($user);
		} catch (CredentialsExpiredException $e) {
			// Ignore CredentialsExpiredException
		}
	}
}