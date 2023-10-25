<?php declare(strict_types=1);

namespace Circli\Console\Tests\Fixtures;

use Circli\Console\Definition;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class CommandDefinition extends Definition
{
	protected function configure(): void
	{
		$this->setName('test:foo');
		$this->setAliases(['foo']);
		$this->setDescription('test description');
		$this->addArgument('test', InputArgument::REQUIRED);
		$this->addUsage('test');
		$this->addOption('force', 'f', InputOption::VALUE_NONE);
		$this->setCommand(static function() {
			echo 'default definition';
			return 0;
		});
	}

	public function transformInput(InputInterface $input, OutputInterface $output): InputInterface
	{
		return new CustomInput();
	}
}
