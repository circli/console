<?php

namespace Circli\Console\Tests;

use Circli\Console\Command;
use Circli\Console\SimpleCommandResolver;
use Circli\Console\Tests\Fixtures\CommandDefinition;
use Circli\Console\Tests\Fixtures\COMMANDWithoutNameDefinition;
use Circli\Console\Tests\Fixtures\CustomInput;
use Circli\Console\Tests\Fixtures\InvalidDefinition;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class CommandTest extends TestCase
{
	public function testCustomInput(): void
	{
		$definition = new CommandDefinition();
		$definition->setCommand(function ($input) {
			$this->assertInstanceOf(CustomInput::class, $input);
			return 0;
		});

		$command = new Command($definition, new SimpleCommandResolver());
		$command->run(new ArrayInput(['test' => '1']), new NullOutput());
	}

	public function testInvalidDefinition(): void
	{
		$definition = new InvalidDefinition();

		$this->expectException(\InvalidArgumentException::class);

		new Command($definition, new SimpleCommandResolver());
	}

	public function testSetInvalidCommand(): void
	{
		$definition = new InvalidDefinition();
		$this->expectException(\InvalidArgumentException::class);
		$definition->setCommand(1);
	}

	public function testAutoCreateName(): void
	{
		$definition = new COMMANDWithoutNameDefinition();
		$command = new Command($definition, new SimpleCommandResolver());

		$this->assertSame('command-without-name', $command->getName());
	}
}
