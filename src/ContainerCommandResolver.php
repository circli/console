<?php declare(strict_types=1);

namespace Circli\Console;

use Psr\Container\ContainerInterface;

final class ContainerCommandResolver implements CommandResolver
{
	/** @var ContainerInterface */
	private $container;

	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

	public function createCommand($command): callable
	{
		if (is_callable($command)) {
			return $command;
		}

		return $this->container->get($command);
	}
}
