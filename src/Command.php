<?php declare(strict_types=1);

namespace Circli\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Command extends \Symfony\Component\Console\Command\Command
{
	/** @var Definition */
	private $definition;
	/** @var CommandResolver */
	private $resolver;

	public function __construct(Definition $definition, CommandResolver $resolver = null)
	{
		if (!$definition->getCommand()) {
			throw new \InvalidArgumentException('Definition don\'t contain any command to execute');
		}
		$commandName = $definition->getName();
		if (!$commandName) {
			$commandName = CommandNaming::classToName(get_class($definition));
		}
		parent::__construct($commandName);
		$this->setDefinition($definition->getDefinition());
		$this->setDescription($definition->getDescription()??'');
		$this->setAliases($definition->getAliases());
		foreach ($definition->getUsages() as $usage) {
			$this->addUsage($usage);
		}
		$this->definition = $definition;
		$this->resolver = $resolver;
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$orgInput = $input;
		$input = $this->definition->transformInput($input);
		if ($input instanceof AbstractInput && !$input->hasInput()) {
			$input->setInput($orgInput);
		}
		$output = $this->definition->transformOutput($output);
		$command = $this->resolver->createCommand($this->definition->getCommand());
		return $command($input, $output);
	}
}
