<?php declare(strict_types=1);

namespace Circli\Console\Tests\Fixtures;

use Circli\Console\Definition;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

final class COMMANDWithoutNameDefinition extends Definition
{
	protected function configure(): void
	{
		$this->setDescription('test description');
		$this->addArgument('test', InputArgument::REQUIRED);
		$this->addUsage('test');
		$this->addOption('force', 'f', InputOption::VALUE_NONE);
		$this->setCommand(static function() {
			echo 'default definition';
			return 0;
		});
	}
}
