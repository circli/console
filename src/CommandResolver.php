<?php declare(strict_types=1);

namespace Circli\Console;

interface CommandResolver
{
	public function createCommand($command): callable;
}
