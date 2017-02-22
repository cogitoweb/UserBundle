<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cogitoweb\UserBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Description of ResponseListener
 *
 * @author Daniele Artico <daniele.artico@cogitoweb.it>
 */
class CredentialsExpiredSubscriber implements EventSubscriberInterface
{
	/**
	 *
	 * @var TokenStorageInterface
	 */
	protected $tokenStorage;

	/**
	 *
	 * @var RouterInterface
	 */
	protected $router;

	/**
	 *
	 * @var TranslatorInterface
	 */
	protected $translator;

	/**
	 *
	 * @var string
	 */
	protected $url;

	/**
	 *
	 * @var string
	 */
	protected $message;

	/**
	 * Constructor
	 * 
	 * @param TokenStorageInterface $tokenStorage
	 * @param RouterInterface       $router
	 * @param TranslatorInterface   $translator
	 */
	public function __construct(TokenStorageInterface $tokenStorage, RouterInterface $router, TranslatorInterface $translator)
	{
		$this->tokenStorage = $tokenStorage;
		$this->router       = $router;
		$this->translator   = $translator;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents()
	{
		return [
			KernelEvents::REQUEST => ['onKernelRequest']
		];
	}

	/**
	 * Handle kernel request event and check if user credentials are expired
	 * 
	 * @param GetResponseEvent $event
	 */
	public function onKernelRequest(GetResponseEvent $event)
	{
		// Token must be valid
		$token = $this->tokenStorage->getToken();

		if (is_null($token)) {
			return;
		}

		// User must be valid
		$user = $token->getUser();

		if (is_null($user)) {
			return;
		}

		// User must be an instance of AdvancedUserInterface to use advanced properties
		if (!$user instanceof AdvancedUserInterface) {
			return;
		}

		// User credentials must be expired
		if ($user->isCredentialsNonExpired()) {
			return;
		}

		// Avoid redirect loop
		if (false !== strpos($event->getRequest()->getRequestUri(), 'change-password')) {
			return;
		}

		// Add error flash and redirect user
		$event->getRequest()->getSession()->getFlashBag()->add('danger', $this->getMessage());
		$event->setResponse(new RedirectResponse($this->getUrl()));
	}

	/**
	 * Get URL
	 * 
	 * @return string URL of Sonata change password form
	 */
	protected function getUrl()
	{
		return $this->router->generate('sonata_user_change_password');
	}

	/**
	 * Get message
	 * 
	 * @return string CredentialsExpiredException message
	 */
	protected function getMessage()
	{
		$exception = new CredentialsExpiredException();

		return $this->translator->trans($exception->getMessageKey(), [], 'security');
	}
}