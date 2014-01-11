<?php namespace mjolnir\access\tests;

use \mjolnir\access\UserCollection;

class UserCollectionTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\UserCollection'));
	}

	// @todo tests for \mjolnir\access\UserCollection

} # test
