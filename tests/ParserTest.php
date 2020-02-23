<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\SteamId;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Throwable;
use UnexpectedValueException;

class ParserTest extends TestCase
{
	/**
	 * @return list<array{0:string, 1:class-string<Throwable>, 2:string}>
	 */
	public function dataProviderFromStringFailure() : array
	{
		return [
			[
				'a',
				InvalidArgumentException::class,
				(
					'Argument 1 passed to ' .
					Parser::class .
					'::FromString() must be an integer-string!'
				),
			],
			[
				'409827566090715136',
				InvalidArgumentException::class,
				(
					'Argument 1 passed to ' .
					Parser::class .
					'::FromString() must be less than or equal to ' .
					Parser::MAX .
					'!'
				),
			],
			[
				'49539595901075456',
				UnexpectedValueException::class,
				sprintf(
					'Type was expected to be in the range 0-%s, %s found!',
					SteamId::TYPE_MAX,
					SteamId::TYPE_MAX + 1
				),
			],
		];
	}

	/**
	 * @dataProvider dataProviderFromStringFailure
	 *
	 * @covers \SignpostMarv\SteamId\Parser::__construct()
	 * @covers \SignpostMarv\SteamId\Parser::FromString()
	 *
	 * @param class-string<Throwable> $exception_type
	 */
	public function test_data_provider_from_string_failure(
		string $id,
		string $exception_type,
		string $exception_message
	) : void {
		$parser = new Parser();

		static::expectException($exception_type);
		static::expectExceptionMessage($exception_message);

		$parser->FromString($id);
	}

	/**
	 * @dataProvider dataProviderFromStringFailure
	 *
	 * @covers \SignpostMarv\SteamId\Parser::__construct()
	 * @covers \SignpostMarv\SteamId\Parser::FromString()
	 * @covers \SignpostMarv\SteamId\SteamId::__construct()
	 */
	public function test_from_string() : void
	{
		$id = (new Parser())->FromString('76561197960287930');

		static::assertSame(0, $id->y);
		static::assertSame(11101, $id->account);
		static::assertSame(1, $id->instance);
		static::assertSame(1, $id->type);
		static::assertSame(1, $id->universe);
	}

	/**
	 * @dataProvider dataProviderFromStringFailure
	 *
	 * @covers \SignpostMarv\SteamId\Parser::__construct()
	 * @covers \SignpostMarv\SteamId\Parser::FromSteamId()
	 * @covers \SignpostMarv\SteamId\SteamId::__construct()
	 */
	public function test_from_steam_id() : void
	{
		$id = (new Parser())->FromSteamId(new SteamId(0, 11101, 1, 1, 1));

		static::assertSame('76561197960287930', $id);
	}

	/**
	 * @dataProvider dataProviderFromStringFailure
	 *
	 * @covers \SignpostMarv\SteamId\Parser::__construct()
	 * @covers \SignpostMarv\SteamId\Parser::FromString()
	 * @covers \SignpostMarv\SteamId\Parser::ToSteamCommunityUrl()
	 * @covers \SignpostMarv\SteamId\SteamId::__construct()
	 */
	public function test_to_steam_community_url() : void
	{
		$parser = new Parser();

		$id = $parser->FromString('76561197960287930');

		static::assertSame(
			'https://steamcommunity.com/profiles/76561197960287930',
			$parser->ToSteamCommunityUrl($id)
		);
	}

	/**
	 * @dataProvider dataProviderFromStringFailure
	 *
	 * @covers \SignpostMarv\SteamId\Parser::__construct()
	 * @covers \SignpostMarv\SteamId\Parser::ToSteamCommunityUrl()
	 * @covers \SignpostMarv\SteamId\SteamId::__construct()
	 */
	public function test_to_steam_community_url_failure() : void
	{
		$parser = new Parser();

		$id = new SteamId(0, 0, 0, 0, 0);

		static::expectException(InvalidArgumentException::class);
		static::expectExceptionMessage(
			'SteamId::$type of argument 1 passed to ' .
			Parser::class .
			'::ToSteamCommunityUrl() must be one of 1, 7- 0 given!'
		);

		$parser->ToSteamCommunityUrl($id);
	}
}
