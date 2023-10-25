<?php declare(strict_types=1);

namespace Circli\Console;

final class CommandNaming
{
	/**
	 * @var string
	 * @see https://regex101.com/r/DfCWPx/1
	 */
	private const BIG_LETTER_REGEX = '#[A-Z]#';

	/**
	 * @var array<string, int>
	 */
	private const ALLOWED_ENDINGS = [
		'Command' => 7,
		'Definition' => 10,
	];

	/**
	 * Converts:
	 *  "SomeClass\SomeSuperCommand" → "some-super"
	 *  "SomeClass\SOMESuperCommand" → "some-super"
	 *  "SomeClass\SomeSuperDefinition" → "some-super"
	 */
	public static function classToName(string $class): string
	{
		$rawCommandName = $shortClassName = self::getShortClassName($class);

		foreach (self::ALLOWED_ENDINGS as $ending => $length) {
			if (substr($shortClassName, -$length) !== $ending) {
				continue;
			}
			$rawCommandName = substr($shortClassName, 0, -$length);
			break;
		}

		// ECSCommand => ecs
		for ($i = 0; $i < strlen($rawCommandName); ++$i) {
			if (ctype_upper($rawCommandName[$i]) && self::isFollowedByUpperCaseLetterOrNothing($rawCommandName, $i)) {
				$rawCommandName[$i] = strtolower($rawCommandName[$i]);
			} else {
				break;
			}
		}

		$rawCommandName = lcfirst($rawCommandName);

		return (string)preg_replace_callback(self::BIG_LETTER_REGEX, function (array $matches): string {
			return '-' . strtolower($matches[0]);
		}, $rawCommandName);
	}

	private static function getShortClassName(string $class): string
	{
		$classParts = explode('\\', $class);

		return (string) array_pop($classParts);
	}

	private static function isFollowedByUpperCaseLetterOrNothing(string $string, int $position): bool
	{
		// this is the last letter
		if (! isset($string[$position + 1])) {
			return true;
		}

		// next letter is uppercase
		return ctype_upper($string[$position + 1]);
	}
}
