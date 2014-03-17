<?php namespace mjolnir\access\tests;

use \mjolnir\access\Trait_SecurityTokenLib;

class Trait_SecurityTokenLib_Tester
{
	use Trait_SecurityTokenLib;
}

class Trait_SecurityTokenLibTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\trait_exists('\mjolnir\access\Trait_SecurityTokenLib'));
	}

	// @todo tests for \mjolnir\access\Trait_SecurityTokenLib

} # test
