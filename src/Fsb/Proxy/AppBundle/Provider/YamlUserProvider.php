<?php

namespace Fsb\Proxy\AppBundle\Provider;

use SplFileInfo;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Yaml\Parser as YamlParser;
use Symfony\Component\Filesystem\Filesystem;

use Fsb\Proxy\AppBundle\Model\User;

class YamlUserProvider implements UserProviderInterface
{
	private $parser;
	private $filePath;

	public function __construct($filePath)
	{
		$fileSystem = new FileSystem();

		if (!$fileSystem->exists($filePath)) {
			$fileSystem->touch($filePath);
			$fileSystem->chmod($filePath, 0755);
		}

		$this->filePath = $filePath;

		return $this;
	}

    public function loadUserByUsername($username)
    {
		$usersFileInfo = new SplFileInfo($this->filePath);

		$this->parser = new YamlParser();
		$this->usersFile = $usersFileInfo->openFile('r');

		$content = '';
		while (!$this->usersFile->eof()) {
			$content .= $this->usersFile->fgets();
		}

		$users = $this->parser->parse($content);

		if ($users && array_key_exists($username, $users)) {
			return new User($username, $users[$username]['password'], null, array('ROLE_USER'));
		}

        throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
    }

    public function refreshUser(UserInterface $user)
    {
		$class = get_class($user);

        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

	public function supportsClass($class)
	{
		return ('Fsb\Proxy\AppBundle\Model\User' === $class) || (is_subclass_of($class, 'Fsb\Proxy\AppBundle\Model\User'));
	}
}
