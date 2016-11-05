<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cogitoweb\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Description of NewUserPassword
 *
 * @author Daniele Artico <daniele.artico@cogitoweb.it>
 * 
 * @Annotation
 */
class NewUserPassword extends Constraint
{
	/**
	 *
	 * @var string
	 */
	public $message = 'This value should be different from user\'s current password.';

	/**
	 *
	 * @var string
	 */
	public $service = 'security.validator.new_user_password';

	/**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return $this->service;
    }
}