<?php declare(strict_types=1);

namespace Circli\Console;

use Circli\Console\Input\InputTrait;
use Symfony\Component\Console\Input\InputInterface;

abstract class AbstractInput implements InputInterface
{
	use InputTrait;
}
