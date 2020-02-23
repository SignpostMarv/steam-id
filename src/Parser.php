<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\SteamId;

use function array_map;
use function assert;
use function ctype_digit;
use InvalidArgumentException;
use function preg_match;
use SignpostMarv\Brick\Math\Calculator;
use SignpostMarv\Brick\Math\Calculator\NativeCalculator;
use function str_pad;
use function strcmp;
use UnexpectedValueException;

class Parser
{
	const MAX = '409827566090715135';

	const MAX_BINARY = '0000010110101111111111111111111111111111111111111111111111111111';

	const BIT_LENGTH = 64;

	const UNIVERSE_LENGTH = 8;

	const TYPE_LENGTH = 4;

	const INSTANCE_LENGTH = 20;

	const ACCOUNT_LENGTH = 31;

	const SUPPORTED_URL_TYPES = [
		1 => ['0110000100000000', 'profiles'],
		7 => ['0170000000000000', 'groups'],
	];

	const EXPECT_MATCH = 1;

	private Calculator $calculator;

	public function __construct(Calculator $calculator = null)
	{
		$this->calculator = $calculator ?: new NativeCalculator();
	}

	public function FromString(string $id) : SteamId
	{
		if ( ! ctype_digit($id)) {
			throw new InvalidArgumentException(
				'Argument 1 passed to ' .
				__METHOD__ .
				'() must be an integer-string!'
			);
		}

		$base2 = str_pad(
			$this->calculator->toBase($id, 2),
			self::BIT_LENGTH,
			'0',
			STR_PAD_LEFT
		);

		if (1 === strcmp($base2, self::MAX_BINARY)) {
			throw new InvalidArgumentException(
				'Argument 1 passed to ' .
				__METHOD__ .
				'() must be less than or equal to ' .
				self::MAX .
				'!'
			);
		}

		$match = preg_match(
			'/^([01]{8})([01]{4})([01]{20})([01]{31})(0|1)$/',
			$base2,
			$matches
		);

		assert(self::EXPECT_MATCH === $match, new UnexpectedValueException(
			'Binary SteamId was not of expected format!'
		));

		array_shift($matches);

		[
			$universe,
			$type,
			$instance,
			$account,
			$y,
		] = array_map(
			function (string $chunk) : int {
				return (int) $this->calculator->fromBase($chunk, 2);
			},
			$matches
		);

		if ($type > SteamId::TYPE_MAX) {
			throw new UnexpectedValueException(sprintf(
				'Type was expected to be in the range 0-%s, %s found!',
				SteamId::TYPE_MAX,
				$type
			));
		}

		/**
		 * @var array{0:0|1, 1:0|1|2|3|4|5|6|7|8|9|10, 2:0|1|2|3|4|5}
		 */
		$nudge = [$y, $type, $universe];

		[$y, $type, $universe] = $nudge;

		return new SteamId($y, $account, $instance, $type, $universe);
	}

	public function FromSteamId(SteamId $id) : string
	{
		[
			$universe,
			$type,
			$instance,
			$account,
			$y,
		] = array_map(
			function (int $chunk) : string {
				return $this->calculator->toBase((string) $chunk, 2);
			},
			[
				$id->universe,
				$id->type,
				$id->instance,
				$id->account,
				$id->y,
			]
		);

		return $this->calculator->fromBase(
			(
				str_pad($universe, self::UNIVERSE_LENGTH, '0', STR_PAD_LEFT) .
				str_pad($type, self::TYPE_LENGTH, '0', STR_PAD_LEFT) .
				str_pad($instance, self::INSTANCE_LENGTH, '0', STR_PAD_LEFT) .
				str_pad($account, self::ACCOUNT_LENGTH, '0', STR_PAD_LEFT) .
				$y
			),
			2
		);
	}

	public function ToSteamCommunityUrl(SteamId $id) : string
	{
		if ( ! isset(self::SUPPORTED_URL_TYPES[$id->type])) {
			throw new InvalidArgumentException(
				'SteamId::$type of argument 1 passed to ' .
				__METHOD__ .
				'() must be one of ' .
				implode(', ', array_keys(self::SUPPORTED_URL_TYPES)) .
				'- ' .
				(string) $id->type .
				' given!'
			);
		}

		[$id64, $path] = self::SUPPORTED_URL_TYPES[$id->type];

		return sprintf(
			'https://steamcommunity.com/%s/%s',
			rawurlencode($path),
			rawurlencode($this->calculator->add(
				$this->calculator->add(
					$this->calculator->mul('2', (string) $id->account),
					$this->calculator->fromBase($id64, 16)
				),
				(string) $id->y
			))
		);
	}
}
