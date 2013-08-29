<?php namespace mjolnir\access\tests;

use \mjolnir\access\Trait_Controller_MjolnirSignup;

class Trait_Controller_MjolnirSignup_Tester
{
	use Trait_Controller_MjolnirSignup;
}

class Trait_Controller_MjolnirSignupTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\trait_exists('\mjolnir\access\Trait_Controller_MjolnirSignup'));
	}

	// @todo tests for \mjolnir\access\Trait_Controller_MjolnirSignup

} # test
