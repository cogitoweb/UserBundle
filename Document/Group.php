<?php

namespace Cogitoweb\UserBundle\Document;

use Sonata\UserBundle\Document\BaseGroup as BaseGroup;

class Group extends BaseGroup
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