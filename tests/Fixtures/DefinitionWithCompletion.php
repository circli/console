<?php declare(strict_types=1);

namespace Circli\Console\Tests\Fixtures;

use Circli\Console\Definition;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

final class DefinitionWithCompletion extends Definition
{
	protected function configure(): void
	{
		$this->setDescription('test description');
		$this->addArgument(
			'test',
			InputArgument::REQUIRED,
			suggestedValues: ['1', '2'],
		);
		$this->addArgument('file');
		$this->addOption(
			'user',
			mode: InputOption::VALUE_REQUIRED,
			suggestedValues: ['user1', 'user21', 'random1', 'ext2']
		);

		$this->addCompletion('file', function (CompletionInput $input, CompletionSuggestions $suggestions, callable $default) {
			$suggestions->suggestValues(['file1', 'file2', 'dir1']);
		});

		$this->setCommand(static function() {
			echo 'default definition';
			return 0;
		});
	}
}
