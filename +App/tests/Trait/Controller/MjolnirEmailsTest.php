<?php namespace mjolnir\access\tests;

use \mjolnir\access\Trait_Controller_MjolnirEmails;

class Trait_Controller_MjolnirEmails_Tester
{
	use Trait_Controller_MjolnirEmails;
}

class Trait_Controller_MjolnirEmailsTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\trait_exists('\mjolnir\access\Trait_Controller_MjolnirEmails'));
	}

	// @todo tests for \mjolnir\access\Trait_Controller_MjolnirEmails

} # test
