<?php declare(strict_types=1);

namespace Circli\Console;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Definition
{
	private InputDefinition $definition;
	private ?string $name = null;
	private ?string $description = null;
	/** @var list<string> */
	private array $aliases = [];
	/** @var list<string> */
	private array $usages = [];
	/** @var string|callable|null */
	private $command;

	public function __construct()
	{
		$this->definition = new InputDefinition();
		$this->configure();
	}

	/**
	 * Adds an argument.
	 *
	 * @param int|null $mode The argument mode: InputArgument::REQUIRED or InputArgument::OPTIONAL
	 * @param string|string[]|null $default The default value (for InputArgument::OPTIONAL mode only)
	 *
	 * @return $this
	 * @throws InvalidArgumentException When argument mode is not valid
	 */
	public function addArgument(string $name, int $mode = null, string $description = '', $default = null)
	{
		$this->definition->addArgument(new InputArgument($name, $mode, $description, $default));

		return $this;
	}

	/**
	 * Adds an option.
	 *
	 * @param string|list<string>|null $shortcut The shortcuts, can be null, a string of shortcuts delimited by | or an array of shortcuts
	 * @param int|null $mode The option mode: One of the InputOption::VALUE_* constants
	 * @param string|string[]|int|bool|null $default The default value (must be null for InputOption::VALUE_NONE)
	 *
	 * @return $this
	 * @throws InvalidArgumentException If option mode is invalid or incompatible
	 */
	public function addOption(string $name, $shortcut = null, int $mode = null, string $description = '', $default = null)
	{
		$this->definition->addOption(new InputOption($name, $shortcut, $mode, $description, $default));

		return $this;
	}

	/**
	 * Add a command usage example, it'll be prefixed with the command name.
	 *
	 * @return $this
	 */
	public function addUsage(string $usage)
	{
		if ($this->name && strpos($usage, (string)$this->name) !== 0) {
			$usage = sprintf('%s %s', $this->name, $usage);
		}

		$this->usages[] = $usage;

		return $this;
	}

	/**
	 * Sets the aliases for the command.
	 *
	 * @param string[] $aliases An array of aliases for the command
	 * @return $this
	 * @throws InvalidArgumentException When an alias is invalid
	 */
	public function setAliases(iterable $aliases): self
	{
		foreach ($aliases as $alias) {
			$this->validateName($alias);
		}

		$this->aliases = is_array($aliases) ? $aliases : iterator_to_array($aliases);

		return $this;
	}

	/**
	 * Sets the description for the command.
	 *
	 * @return $this
	 */
	public function setDescription(string $description): self
	{
		$this->description = $description;

		return $this;
	}

	/**
	 * Sets the name of the command.
	 *
	 * This method can set both the namespace and the name if
	 * you separate them by a colon (:)
	 *
	 *     $command->setName('foo:bar');
	 *
	 * @return $this
	 * @throws InvalidArgumentException When the name is invalid
	 */
	public function setName(string $name): self
	{
		$this->validateName($name);

		$this->name = $name;

		return $this;
	}

	/**
	 * Validates a command name.
	 *
	 * It must be non-empty and parts can optionally be separated by ":".
	 *
	 * @throws InvalidArgumentException When the name is invalid
	 */
	private function validateName(string $name): void
	{
		if (!preg_match('/^[^\:]++(\:[^\:]++)*$/', $name)) {
			throw new InvalidArgumentException(sprintf('Command name "%s" is invalid.', $name));
		}
	}

	/**
	 * Set command to execute
	 *
	 * Command can be any callable type or a string to be fetched
	 * from container
	 *
	 * @param string|object|callable $command
	 * @return $this
	 * @throws InvalidArgumentException When the type is invalid
	 */
	public function setCommand($command): self
	{
		if (!is_callable($command) && !is_string($command)) {
			$type = function_exists('get_debug_type') ? get_debug_type($command) : gettype($command);
			throw new InvalidArgumentException(sprintf('Command type "%s" is invalid.', $type));
		}

		$this->command = $command;

		return $this;
	}

	public function getDefinition(): InputDefinition
	{
		return $this->definition;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * @return list<string>
	 */
	public function getAliases(): array
	{
		return $this->aliases;
	}

	/**
	 * @return list<string>
	 */
	public function getUsages(): array
	{
		return $this->usages;
	}

	/**
	 * @return callable|string|null
	 */
	public function getCommand()
	{
		return $this->command;
	}

	public function transformInput(InputInterface $input): InputInterface
	{
		return $input;
	}

	public function transformOutput(OutputInterface $output): OutputInterface
	{
		return $output;
	}

	abstract protected function configure(): void;
}
