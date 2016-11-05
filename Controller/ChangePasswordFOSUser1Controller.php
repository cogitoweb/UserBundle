<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cogitoweb\UserBundle\Controller;

use FOS\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Controller\ChangePasswordFOSUser1Controller as SonataChangePasswordFOSUser1Controller;

/**
 * Description of ChangePasswordFOSUser1Controller
 *
 * @author Daniele Artico <daniele.artico@cogitoweb.it>
 */
class ChangePasswordFOSUser1Controller extends SonataChangePasswordFOSUser1Controller
{
	/**
	 * {@inheritdoc}
	 */
	protected function getRedirectionUrl(UserInterface $user)
	{
		return $this->generateUrl('sonata_admin_redirect');
	}
}