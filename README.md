# Circli Console - Wrapper around symfony console

[![Latest Version](https://img.shields.io/github/release/circli/console.svg?style=flat-square)](https://github.com/circli/console/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://github.com/circli/console/workflows/Unit%20Tests/badge.svg)](https://github.com/circli(console/actions)

I created this package to have a more lightweight initialization of symfony console applications.
It's a bit annoying if a class dose auto connect to something remote, and it needs todo that when you list the commands or run something not connected to the remote service.
So I split the definition and command into 2 parts one for the definition and one for the command. And the command is not initialized until it's needed.

## Installation

```
composer require circli/console
```

## Usage

#### Definition

The definition object handles everything related to how the command is called. And **must** extend `\Circli\Console\Definition`.

#### Command

The command can be anything `callable` and **must** return an integer.

So any class you want to use as a command need to implement `__invoke(InputInterface $input, OutputInterface $output): int`. 

### Most basic definition

```php

class ExampleCommandDefinition extends \Circli\Console\Definition
{
    protected function configure(): void
    {
        $this->setName('example:command');
        $this->setCommand(function($input, $output) {
            return 0;
        });
    }
}

$application = new Application();
$application->addDefinition(new ExampleCommandDefinition());

$application->run();
```

### Using custom input

You can transform input into custom input types to have better typehinting and control over what is passed into a command

```php
class ExampleInput extends \Circli\Console\AbstractInput
{
    public function getFrom(): \DateTimeInterface
    {
        $rawDate = $this->getArgument('from') ?: 'now';
        
        return new \DateTimeImmutable($rawDate);
    }
}

class ExampleCommandDefinition extends \Circli\Console\Definition
{
    protected function configure(): void
    {
        $this->setName('example:command');
        $this->addArgument('from', InputArgument::REQUIRED);
        $this->setCommand(function(ExampleInput $input, $output) {
            $from = $input->getFrom();
            return 0;
        });
    }
    
    public function transformInput(InputInterface $input): InputInterface
    {
        return new ExampleInput();
    }

}
```

### Using psr container

This is a basic implementation to get lazy initialization to work.

If you pass in the container command resolver it will try resolving the command when it's needed.

You can write your own resolver logic if you don't want to pass in the container like this

```php
use Circli\Console\Application;
use Circli\Console\ContainerCommandResolver;
use Circli\Console\Definition;

$application = new Application(new ContainerCommandResolver($psr11container));
$application->addDefinition(new class extends Definition {
    protected function configure(): void
    {
        $this->setName('example:command');
        $this->setCommand('keyInContainer');
    }
});
$application->run();
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
