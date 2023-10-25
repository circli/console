<?php declare(strict_types=1);

namespace Circli\Console;

use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class Application extends SymfonyApplication
{
	public function __construct(
		private readonly CommandResolver $resolver = new SimpleCommandResolver(),
	) {
		parent::__construct();
	}

	public function addDefinition(Definition $definition): ?SymfonyCommand
	{
		return $this->add($this->createCommand($definition));
	}

	public function addDefinitions(Definition ...$definitions): void
	{
		foreach ($definitions as $definition) {
			$this->addDefinition($definition);
		}
	}

	protected function createCommand(Definition $definition): SymfonyCommand
	{
		return new Command($definition, $this->resolver);
	}
}
