<?php declare(strict_types=1);

namespace Circli\Console;

interface CommandResolver
{
	/**
	 * @param string|callable|null $command
	 * @return callable
	 */
	public function createCommand($command): callable;
}
