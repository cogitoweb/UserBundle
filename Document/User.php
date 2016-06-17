<?php

namespace Cogitoweb\UserBundle\Document;

use Sonata\UserBundle\Document\BaseUser as BaseUser;

class User extends BaseUser
{
	/**
	 * @var integer $id
	 */
	protected $id;

	/**
	 * Get id
	 *
	 * @return integer $id
	 */
	public function getId()
	{
		return $this->id;
	}
}