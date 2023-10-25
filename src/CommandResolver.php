<?php declare(strict_types=1);

namespace Circli\Console;

interface CommandResolver
{
	/**
	 * @param callable|string|null $command
	 * @return callable
	 */
	public function createCommand(callable|string|null $command): callable;
}
