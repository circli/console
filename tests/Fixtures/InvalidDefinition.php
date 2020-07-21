<?php declare(strict_types=1);

namespace Circli\Console\Tests\Fixtures;

use Circli\Console\Definition;

final class InvalidDefinition extends Definition
{
	protected function configure(): void
	{
		$this->setName('test:invalid');
	}
}
