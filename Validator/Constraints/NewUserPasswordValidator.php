<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cogitoweb\UserBundle\Validator\Constraints;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Description of NewUserPasswordValidator
 *
 * @author Daniele Artico <daniele.artico@cogitoweb.it>
 */
class NewUserPasswordValidator extends ConstraintValidator
{
	/**
	 *
	 * @var TokenStorageInterface
	 */
	protected $tokenStorage;

	/**
	 * 
	 * @var EncoderFactoryInterface
	 */
	protected $encoderFactory;

	/**
	 * Constructor
	 * 
	 * @param TokenStorageInterface   $tokenStorage
	 * @param EncoderFactoryInterface $encoderFactory
	 */
	public function __construct(TokenStorageInterface $tokenStorage, EncoderFactoryInterface $encoderFactory)
	{
		$this->tokenStorage   = $tokenStorage;
		$this->encoderFactory = $encoderFactory;
	}

	/**
	 * {@inheritdoc}
	 */
	public function validate($password, Constraint $constraint)
	{
		if (!$constraint instanceof NewUserPassword) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\NewUserPassword');
        }

        $user = $this->tokenStorage->getToken()->getUser();

        if (!$user instanceof UserInterface) {
            throw new ConstraintDefinitionException('The User object must implement the UserInterface interface.');
        }

        $encoder = $this->encoderFactory->getEncoder($user);

		// New user password must be different from current user password
        if ($encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt())) {
            $this->context->addViolation($constraint->message);
        }
	}
}