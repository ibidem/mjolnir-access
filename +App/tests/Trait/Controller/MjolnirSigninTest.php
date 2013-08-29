<?php namespace mjolnir\access\tests;

use \mjolnir\access\Trait_Controller_MjolnirSignin;

class Trait_Controller_MjolnirSignin_Tester
{
	use Trait_Controller_MjolnirSignin;
}

class Trait_Controller_MjolnirSigninTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\trait_exists('\mjolnir\access\Trait_Controller_MjolnirSignin'));
	}

	// @todo tests for \mjolnir\access\Trait_Controller_MjolnirSignin

} # test
