<?php declare(strict_types=1);

namespace Circli\Console;

final class SimpleCommandResolver implements CommandResolver
{
	public function createCommand(callable|string|null $command): callable
	{
		if (!is_callable($command)) {
			throw new \InvalidArgumentException('Command must be callable');
		}

		return $command;
	}
}
