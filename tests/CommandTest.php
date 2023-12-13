<?php declare(strict_types=1);

namespace Circli\Console\Tests;

use Circli\Console\Command;
use Circli\Console\SimpleCommandResolver;
use Circli\Console\Tests\Fixtures\CommandDefinition;
use Circli\Console\Tests\Fixtures\COMMANDWithoutNameDefinition;
use Circli\Console\Tests\Fixtures\CustomInput;
use Circli\Console\Tests\Fixtures\DefinitionWithCompletion;
use Circli\Console\Tests\Fixtures\InvalidDefinition;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
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
		$this->expectException(\TypeError::class);
		$definition->setCommand(1);
	}

	public function testSetInvalidCommandObject(): void
	{
		$definition = new InvalidDefinition();
		$this->expectException(\InvalidArgumentException::class);
		$definition->setCommand(new class {});
	}

	public function testAutoCreateName(): void
	{
		$definition = new COMMANDWithoutNameDefinition();
		$command = new Command($definition, new SimpleCommandResolver());

		$this->assertSame('command-without-name', $command->getName());
	}

	public function testCompletionFirstArgument()
	{
		$definition = new DefinitionWithCompletion();
		$command = new Command($definition, new SimpleCommandResolver());

		$input = CompletionInput::fromTokens([
			'cmd',
			'1',
		], 1);
		$suggestions = new CompletionSuggestions();

		$input->bind($command->getDefinition());

		$command->complete($input, $suggestions);
		$this->assertCount(2, $suggestions->getValueSuggestions());
	}

	public function testCompletionSecondArgument()
	{
		$definition = new DefinitionWithCompletion();
		$command = new Command($definition, new SimpleCommandResolver());

		$input = CompletionInput::fromTokens([
			'cmd',
			'1',
			'f'
		], 1);
		$suggestions = new CompletionSuggestions();

		$input->bind($command->getDefinition());

		$command->complete($input, $suggestions);
		$this->assertCount(3, $suggestions->getValueSuggestions());
	}

	public function testCompletionOption()
	{
		$definition = new DefinitionWithCompletion();
		$command = new Command($definition, new SimpleCommandResolver());

		$input = CompletionInput::fromTokens([
			'cmd',
			'--user',
		], 1);
		$suggestions = new CompletionSuggestions();

		$input->bind($command->getDefinition());

		$command->complete($input, $suggestions);
		$this->assertCount(4, $suggestions->getValueSuggestions());
	}
}
