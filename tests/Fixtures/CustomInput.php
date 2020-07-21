<?php declare(strict_types=1);

namespace Circli\Console\Tests\Fixtures;

use Circli\Console\AbstractInput;

final class CustomInput extends AbstractInput
{
	public function getTestArgument(): string
	{
		return $this->getArgument('test');
	}
}
