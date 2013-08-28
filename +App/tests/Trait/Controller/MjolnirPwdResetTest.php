<?php namespace mjolnir\access\tests;

use \mjolnir\access\Trait_Controller_MjolnirPwdReset;

class Trait_Controller_MjolnirPwdReset_Tester
{
	use Trait_Controller_MjolnirPwdReset;
}

class Trait_Controller_MjolnirPwdResetTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\trait_exists('\mjolnir\access\Trait_Controller_MjolnirPwdReset'));
	}

	// @todo tests for \mjolnir\access\Trait_Controller_MjolnirPwdReset

} # test
