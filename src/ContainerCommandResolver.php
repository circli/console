<?php declare(strict_types=1);

namespace Circli\Console;

use Psr\Container\ContainerInterface;

final class ContainerCommandResolver implements CommandResolver
{
	private ContainerInterface $container;

	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

	public function createCommand($command): callable
	{
		if (is_callable($command)) {
			return $command;
		}

		if (!is_string($command)) {
			throw new \InvalidArgumentException('Command must be string to be resolved from container');
		}
		if ($this->container->has($command)) {
			throw new \RuntimeException('Can\'t find command in container');
		}

		$resolvedCommand = $this->container->get($command);
		if (!is_callable($resolvedCommand)) {
			throw new \InvalidArgumentException('Command must be callable');
		}

		return $resolvedCommand;
	}
}
