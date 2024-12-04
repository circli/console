<?php declare(strict_types=1);

namespace Circli\Console\Input;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;

trait InputTrait
{
	protected ?InputInterface $input = null;

	public function setInput(InputInterface $input): void
	{
		$this->input = $input;
	}

	public function hasInput(): bool
	{
		return $this->input instanceof InputInterface;
	}

	public function getFirstArgument(): ?string
	{
		return $this->input?->getFirstArgument();
	}

	/**
	 * @param string|list<string> $values
	 */
	public function hasParameterOption($values, bool $onlyParams = false): bool
	{
		if (!$this->input) {
			return false;
		}
		return $this->input->hasParameterOption($values, $onlyParams);
	}

	/**
	 * @param string|list<string> $values
	 * @param string|bool|int|float|mixed[]|null $default
	 */
	public function getParameterOption($values, $default = false, bool $onlyParams = false): mixed
	{
		if (!$this->input) {
			return $default;
		}
		return $this->input->getParameterOption($values, $default, $onlyParams);
	}

	public function bind(InputDefinition $definition): void
	{
		$this->input?->bind($definition);
	}

	public function validate(): void
	{
		$this->input?->validate();
	}

	/**
	 * @return array<string|bool|int|float|mixed[]|null>
	 */
	public function getArguments(): array
	{
		if (!$this->input) {
			return [];
		}
		return $this->input->getArguments();
	}

	public function getArgument(string $name): mixed
	{
		if (!$this->input) {
			throw new InvalidArgumentException(sprintf('The "%s" argument does not exist.', $name));
		}
		return $this->input->getArgument($name);
	}

	/**
	 * @param mixed $value
	 */
	public function setArgument(string $name, $value): void
	{
		$this->input?->setArgument($name, $value);
	}

	public function hasArgument(string $name): bool
	{
		if (!$this->input) {
			return false;
		}
		return $this->input->hasArgument($name);
	}

	/**
	 * @return array<string|bool|int|float|mixed[]|null>
	 */
	public function getOptions(): array
	{
		if (!$this->input) {
			return [];
		}
		return $this->input->getOptions();
	}

	/**
	 * @return mixed
	 */
	public function getOption(string $name): mixed
	{
		if (!$this->input) {
			throw new InvalidArgumentException(sprintf('The "%s" option does not exist.', $name));
		}
		return $this->input->getOption($name);
	}

	/**
	 * @param mixed $value
	 */
	public function setOption(string $name, $value): void
	{
		$this->input?->setOption($name, $value);
	}

	public function hasOption(string $name): bool
	{
		if (!$this->input) {
			return false;
		}
		return $this->input->hasOption($name);
	}

	public function isInteractive(): bool
	{
		if (!$this->input) {
			return false;
		}
		return $this->input->isInteractive();
	}

	public function setInteractive(bool $interactive): void
	{
		$this->input?->setInteractive($interactive);
	}

	public function __toString(): string
	{
		return $this->input?->__toString() ?? '';
	}
}