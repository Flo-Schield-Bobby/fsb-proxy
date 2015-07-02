<?php

namespace Fsb\Proxy\AppBundle\Command;

use SplFileInfo;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Dumper as YamlDumper;

use Fsb\Proxy\AppBundle\Model\User;

class CreateUserCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('fsb-proxy:users:create')
			->setDescription('Create a new user')
			->addArgument(
				'name',
				InputArgument::OPTIONAL,
				'What is the new user\'s name?'
			)
			->addArgument(
				'password',
				InputArgument::OPTIONAL,
				'What is the new user\'s password?'
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output = new SymfonyStyle($input, $output);
		$container = $this->getContainer();
		$input->isInteractive() ? $output->title('FsbProxy users helper') : $output->newLine();

		// Retrieve username or ask for it
		$username = $input->getArgument('name');

		if (!$username) {
			if (!$input->isInteractive()) {
				$output->error('The username must not be empty.');

				return 1;
			}

			$usernameQuestion = $this->createUsernameQuestion($input, $output);
			$username = $output->askQuestion($usernameQuestion);
		}

		// Retrieve password or ask for it
		$password = $input->getArgument('password');

		if (!$password) {
			if (!$input->isInteractive()) {
				$output->error('The password must not be empty.');

				return 1;
			}
			$passwordQuestion = $this->createPasswordQuestion($input, $output);
			$password = $output->askQuestion($passwordQuestion);
		}

		// Encrypt user password
		$user = new User($username, null, null, array('ROLE_USER'));

		$encoder = $container->get('security.password_encoder');
		$user->setPassword($encoder->encodePassword($user, $password));

		// Save user in users file
		$filePath = $container->getParameter('users_provider_file_path');

		$fileSystem = new FileSystem();

		if (!$fileSystem->exists($filePath)) {
			$fileSystem->touch($filePath);
			$fileSystem->chmod($filePath, 0755);
		}

		$usersFileInfo = new SplFileInfo($filePath);
		$usersFile = $usersFileInfo->openFile('a+');

		$dumper = new YamlDumper();
		$yaml = $dumper->dump(array(
			$user->getUsername() => array(
				'password' => $user->getPassword()
			)
		), 2);

		$usersFile->fwrite($yaml);

		// Output result
		$output->table(array(
			'Info',
			'Value'
		), array(
			array('Username', $user->getUsername()),
			array('Encoder used', get_class($encoder)),
			array('Encoded password', $user->getPassword()),
		));

		$output->success('User successfully created');
	}

	/**
	 * Create the password question to ask the user for the password to be encoded.
	 *
	 * @return Question
	 */
	private function createUsernameQuestion()
	{
		$passwordQuestion = new Question('What is the new user\'s name?');

		return $passwordQuestion->setValidator(function ($value) {
			if ('' === trim($value)) {
				throw new \Exception('The username must not be empty.');
			}

			return $value;
		})->setHidden(true)->setMaxAttempts(20);
	}

	/**
	 * Create the password question to ask the user for the password to be encoded.
	 *
	 * @return Question
	 */
	private function createPasswordQuestion()
	{
		$passwordQuestion = new Question('What is the new user\'s password?');

		return $passwordQuestion->setValidator(function ($value) {
			if ('' === trim($value)) {
				throw new \Exception('The password must not be empty.');
			}

			return $value;
		})->setHidden(true)->setMaxAttempts(20);
	}
}
