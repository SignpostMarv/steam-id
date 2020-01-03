<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\SteamId;

use InvalidArgumentException;

/**
* @psalm-type y = 0|1
* @psalm-type type = 0|1|2|3|4|5|6|7|8|9|10
* @psalm-type universe = 0|1|2|3|4|5
*/
class SteamId
{
	const TYPE_MAX = 10;

	const UNIVERSE_MAX = 5;

	const ACCOUNT_MAX = 2147483647;

	const INSTANCE_MAX = 1048575;

	/**
	* @readonly
	*
	* @var y
	*/
	public int $y;

	/**
	* @readonly
	*/
	public int $account;

	/**
	* @readonly
	*/
	public int $instance;

	/**
	* @readonly
	*
	* @var type
	*/
	public int $type;

	/**
	* @readonly
	*
	* @var universe
	*/
	public int $universe;

	/**
	* @param y $y
	* @param type $type
	* @param universe $universe
	*/
	public function __construct(
		int $y,
		int $account,
		int $instance,
		int $type,
		int $universe
	) {
		if ($account < 0 || $account > self::ACCOUNT_MAX) {
			throw new InvalidArgumentException(sprintf(
				(
					'Argument 2 passed to ' .
					__METHOD__ .
					'() must be in the range 0 and %s, %s given.'
				),
				self::ACCOUNT_MAX,
				$account
			));
		} elseif ($instance < 0 || $instance > self::INSTANCE_MAX) {
			throw new InvalidArgumentException(sprintf(
				(
					'Argument 3 passed to ' .
					__METHOD__ .
					'() must be in the range 0 and %s, %s given.'
				),
				self::INSTANCE_MAX,
				$instance
			));
		}

		$this->y = $y;
		$this->account = $account;
		$this->instance = $instance;
		$this->type = $type;
		$this->universe = $universe;
	}

	public function __toString() : string
	{
		return sprintf(
			'STEAM_%u:%u:%u',
			$this->universe,
			$this->y,
			$this->account
		);
	}
}
