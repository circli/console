<?php declare(strict_types=1);

namespace Circli\Console;

use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class Application extends SymfonyApplication
{
	/** @var CommandResolver */
	private $resolver;

	public function __construct(CommandResolver $resolver = null)
	{
		parent::__construct();
		$this->resolver = $resolver ?? new SimpleCommandResolver();
	}

	public function addDefinition(Definition $definition): SymfonyCommand
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
