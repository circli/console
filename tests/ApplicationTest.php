<?php declare(strict_types=1);

namespace Circli\Console\Tests;

use Circli\Console\Application;
use Circli\Console\SimpleCommandResolver;
use Circli\Console\Tests\Fixtures\CommandDefinition;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
	public function testList(): void
	{
		$application = new Application(new SimpleCommandResolver());
		$application->addDefinitions(new CommandDefinition());

		$this->assertTrue($application->has('test:foo'));
	}
}
