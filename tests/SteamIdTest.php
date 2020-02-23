<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\SteamId;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class SteamIdTest extends TestCase
{
	/**
	* @return list<array{0:int}>
	*/
	public function dataProviderAccountFailure() : array
	{
		return [
			[-1],
			[SteamId::ACCOUNT_MAX + 1],
		];
	}

	/**
	* @dataProvider dataProviderAccountFailure
	*
	* @covers \SignpostMarv\SteamId\SteamId::__construct()
	*/
	public function test_account_failure(int $value) : void
	{
		static::expectException(InvalidArgumentException::class);
		static::expectExceptionMessage(sprintf(
			(
				'Argument 2 passed to ' .
				SteamId::class .
				'::__construct() must be in the range 0 and %s, %s given.'
			),
			SteamId::ACCOUNT_MAX,
			$value
		));

		new SteamId(0, $value, 0, 0, 0);
	}

	/**
	* @return list<array{0:int}>
	*/
	public function dataProviderInstanceFailure() : array
	{
		return [
			[-1],
			[SteamId::INSTANCE_MAX + 1],
		];
	}

	/**
	* @dataProvider dataProviderInstanceFailure
	*
	* @covers \SignpostMarv\SteamId\SteamId::__construct()
	*/
	public function test_instance_failure(int $value) : void
	{
		static::expectException(InvalidArgumentException::class);
		static::expectExceptionMessage(sprintf(
			(
				'Argument 3 passed to ' .
				SteamId::class .
				'::__construct() must be in the range 0 and %s, %s given.'
			),
			SteamId::INSTANCE_MAX,
			$value
		));

		new SteamId(0, 0, $value, 0, 0);
	}

	/**
	* @dataProvider dataProviderInstanceFailure
	*
	* @covers \SignpostMarv\SteamId\SteamId::__construct()
	* @covers \SignpostMarv\SteamId\SteamId::__toString()
	*/
	public function test_steam_id_string() : void
	{
		static::assertSame(
			'STEAM_1:0:11101',
			(string) new SteamId(
				0,
				11101,
				1,
				1,
				1
			)
		);
	}
}
