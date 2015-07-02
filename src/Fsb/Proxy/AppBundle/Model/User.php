<?php

namespace Fsb\Proxy\AppBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\Common\Collections\ArrayCollection;

class User implements UserInterface
{
	/**
	 * @var string
	 */
	private $username;

	/**
	 * @var string
	 */
	private $password;

	/**
	 * @var string
	 */
	private $salt;

	/**
	 * @var ArrayCollection
	 */
	private $roles;

	/**
	 * Public constructor
	 * Set default values
	 *
	 * @param string $username
	 * @param string $password
	 * @param string $salt
	 * @param ArrayCollection $roles
	 * @return User
	 */
	public function __construct($username, $password, $salt, array $roles = null)
	{
		$this->username = $username;
		$this->password = $password;
		$this->salt = $salt;
		$this->roles = new ArrayCollection($roles);

		return $this;
	}

	/**
	 * Set username
	 *
	 * @param string $username
	 * @return User
	 */
	public function setUsername($username)
	{
		$this->username = $username;

		return $this;
	}

	/**
	 * Get username
	 *
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}


	/**
	 * Set password
	 *
	 * @param string $password
	 * @return User
	 */
	public function setPassword($password)
	{
		$this->password = $password;

		return $this;
	}

	/**
	 * Get password
	 *
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}


	/**
	 * Set salt
	 *
	 * @param string $salt
	 * @return User
	 */
	public function setSalt($salt)
	{
		$this->salt = $salt;

		return $this;
	}

	/**
	 * Get salt
	 *
	 * @return string
	 */
	public function getSalt()
	{
		return $this->salt;
	}

	/**
	 * Set roles
	 *
	 * @param ArrayCollection $roles
	 * @return User
	 */
	public function setRoles(ArrayCollection $roles)
	{
		$this->roles = $roles;

		return $this;
	}

	/**
	 * Add role
	 *
	 * @param string $role
	 * @return User
	 */
	public function addRole($role)
	{
		if (!$this->roles->contains($role)) {
			$this->roles->add($role);
		}

		return $this;
	}

	/**
	 * Remove role
	 *
	 * @param string $role
	 * @return User
	 */
	public function removeRole($role)
	{
		$this->roles->removeElement($role);

		return $this;
	}

	/**
	 * Get roles
	 *
	 * @return array
	 */
	public function getRoles()
	{
		return $this->roles->toArray();
	}

	public function eraseCredentials()
	{
	}
}
