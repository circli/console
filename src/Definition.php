<?php declare(strict_types=1);

namespace Circli\Console;

use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Completion\Suggestion;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Definition
{
	private readonly InputDefinition $definition;
	private ?string $name = null;
	private ?string $description = null;
	/** @var list<string> */
	private array $aliases = [];
	/** @var list<string> */
	private array $usages = [];
	/** @var string|callable|null */
	private $command;
	/** @var array<string|callable> */
	private array $completions = [];

	public function __construct()
	{
		$this->definition = new InputDefinition();
		$this->configure();
	}

	/**
	 * Adds an argument.
	 *
	 * @param int|null $mode The argument mode: InputArgument::REQUIRED or InputArgument::OPTIONAL
	 * @param string|bool|int|float|array<mixed>|null $default The default value (for InputArgument::OPTIONAL mode only)
	 * @param list<string>|\Closure(CompletionInput,CompletionSuggestions):list<string|Suggestion> $suggestedValues The values used for input completion
	 *
	 * @return $this
	 * @throws InvalidArgumentException When argument mode is not valid
	 */
	public function addArgument(
		string $name,
		int $mode = null,
		string $description = '',
		string|bool|int|float|array $default = null,
		\Closure|array $suggestedValues = [],
	): static {
		$this->definition->addArgument(new InputArgument($name, $mode, $description, $default, $suggestedValues));

		return $this;
	}

	/**
	 * Adds an option.
	 *
	 * @param string|list<string>|null $shortcut The shortcuts, can be null, a string of shortcuts delimited by | or an array of shortcuts
	 * @param int|null $mode The option mode: One of the InputOption::VALUE_* constants
	 * @param bool|int|string|string[]|null $default The default value (must be null for InputOption::VALUE_NONE)
	 * @param list<string>|\Closure(CompletionInput,CompletionSuggestions):list<string|Suggestion> $suggestedValues The values used for input completion
	 *
	 * @throws InvalidArgumentException If option mode is invalid or incompatible
	 */
	public function addOption(
		string $name,
		array|string $shortcut = null,
		int $mode = null,
		string $description = '',
		array|bool|int|string $default = null,
		\Closure|array $suggestedValues = [],
	): static {
		$this->definition->addOption(new InputOption($name, $shortcut, $mode, $description, $default, $suggestedValues));

		return $this;
	}

	/**
	 * Add a command usage example, it'll be prefixed with the command name.
	 */
	public function addUsage(string $usage): static
	{
		if ($this->name && !str_starts_with($usage, (string)$this->name)) {
			$usage = sprintf('%s %s', $this->name, $usage);
		}

		$this->usages[] = $usage;

		return $this;
	}

	/**
	 * Sets the aliases for the command.
	 *
	 * @param string[] $aliases An array of aliases for the command
	 * @throws InvalidArgumentException When an alias is invalid
	 */
	public function setAliases(iterable $aliases): static
	{
		foreach ($aliases as $alias) {
			$this->validateName($alias);
		}

		$this->aliases = is_array($aliases) ? $aliases : iterator_to_array($aliases);

		return $this;
	}

	/**
	 * Sets the description for the command.
	 */
	public function setDescription(string $description): static
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
	 * @throws InvalidArgumentException When the name is invalid
	 */
	public function setName(string $name): static
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
	 * @param callable|object|string $command
	 * @return $this
	 * @throws InvalidArgumentException When the type is invalid
	 */
	public function setCommand(callable|object|string $command): static
	{
		if (!is_callable($command) && !is_string($command)) {
			$type = function_exists('get_debug_type') ? get_debug_type($command) : gettype($command);
			throw new InvalidArgumentException(sprintf('Command type "%s" is invalid.', $type));
		}

		$this->command = $command;

		return $this;
	}

	/**
	 * @param callable(CompletionInput $input, CompletionSuggestions $suggestions, callable $default): void|string $completion
	 * @return $this
	 */
	public function setCompletion(callable|string $completion): static
	{
		$this->completions['__ROOT'] = $completion;

		return $this;
	}

	/**
	 * @return array<string, callable|string>
	 */
	public function getCompletions(): array
	{
		return $this->completions;
	}

	/**
	 * @param callable(CompletionInput $input, CompletionSuggestions $suggestions, callable $default): void|string $completion
	 * @return $this
	 */
	public function addCompletion(string $argumentOrOption, callable|string $completion): static
	{
		$this->completions[$argumentOrOption] = $completion;
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
	public function getCommand(): callable|string|null
	{
		return $this->command;
	}

	public function transformInput(InputInterface $input, OutputInterface $output): InputInterface
	{
		return $input;
	}

	public function transformOutput(OutputInterface $output, InputInterface $input): OutputInterface
	{
		return $output;
	}

	abstract protected function configure(): void;
}
