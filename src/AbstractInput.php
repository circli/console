<?php declare(strict_types=1);

namespace Circli\Console;

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;

abstract class AbstractInput implements InputInterface
{
	/** @var InputInterface */
	protected $input;

	public function setInput(InputInterface $input): void
	{
		$this->input = $input;
	}

	public function hasInput(): bool
	{
		return $this->input instanceof InputInterface;
	}

	public function getFirstArgument()
	{
		return $this->input->getFirstArgument();
	}

	public function hasParameterOption($values, bool $onlyParams = false)
	{
		return $this->input->hasParameterOption($values, $onlyParams);
	}

	public function getParameterOption($values, $default = false, bool $onlyParams = false)
	{
		return $this->input->getParameterOption($values, $default, $onlyParams);
	}

	public function bind(InputDefinition $definition)
	{
		return $this->input->bind($definition);
	}

	public function validate()
	{
		return $this->input->validate();
	}

	public function getArguments()
	{
		return $this->input->getArguments();
	}

	public function getArgument(string $name)
	{
		return $this->input->getArgument($name);
	}

	public function setArgument(string $name, $value)
	{
		return $this->input->setArgument($name, $value);
	}

	public function hasArgument($name)
	{
		return $this->input->hasArgument($name);
	}

	public function getOptions()
	{
		return $this->input->getOptions();
	}

	public function getOption(string $name)
	{
		return $this->input->getOption($name);
	}

	public function setOption(string $name, $value)
	{
		$this->input->setOption($name, $value);
	}

	public function hasOption(string $name)
	{
		return $this->input->hasOption($name);
	}

	public function isInteractive()
	{
		return $this->input->isInteractive();
	}

	public function setInteractive(bool $interactive)
	{
		return $this->input->setInteractive($interactive);
	}
}
