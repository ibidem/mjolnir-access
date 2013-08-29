<?php namespace mjolnir\access\tests;

use \mjolnir\access\Trait_Model_SecurityToken;

class Trait_Model_SecurityToken_Tester
{
	use Trait_Model_SecurityToken;
}

class Trait_Model_SecurityTokenTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\trait_exists('\mjolnir\access\Trait_Model_SecurityToken'));
	}

	// @todo tests for \mjolnir\access\Trait_Model_SecurityToken

} # test
