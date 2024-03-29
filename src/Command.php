<?php declare(strict_types=1);

namespace Circli\Console;

use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Command extends \Symfony\Component\Console\Command\Command
{
	private readonly Definition $definition;

	public function __construct(
		Definition $definition,
		private readonly CommandResolver $resolver = new SimpleCommandResolver(),
	) {
		if (!$definition->getCommand()) {
			throw new \InvalidArgumentException('Definition don\'t contain any command to execute');
		}
		$commandName = $definition->getName();
		if (!$commandName) {
			$commandName = CommandNaming::classToName(get_class($definition));
		}
		parent::__construct($commandName);
		$this->setDefinition($definition->getDefinition());
		$this->setDescription($definition->getDescription() ?? '');
		$this->setAliases($definition->getAliases());
		foreach ($definition->getUsages() as $usage) {
			$this->addUsage($usage);
		}
		$this->definition = $definition;
	}

	public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
	{
		$completions = $this->definition->getCompletions();
		$fallback = fn () => parent::complete($input, $suggestions);
		if (isset($completions[$input->getCompletionName()])) {
			$completionCallable = $this->resolver->createCommand($completions[$input->getCompletionName()]);
			$completionCallable($input, $suggestions, $fallback);
		}
		elseif (isset($completions['__ROOT'])) {
			$completionCallable = $this->resolver->createCommand($completions['__ROOT']);
			$completionCallable($input, $suggestions, $fallback);
		}
		else {
			parent::complete($input, $suggestions);
		}
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$orgInput = $input;
		$input = $this->definition->transformInput($input, $output);
		if ($input instanceof AbstractInput && !$input->hasInput()) {
			$input->setInput($orgInput);
		}
		$output = $this->definition->transformOutput($output, $input);
		$command = $this->resolver->createCommand($this->definition->getCommand());
		return $command($input, $output);
	}
}
