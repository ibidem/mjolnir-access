<?php namespace mjolnir\access\tests;

use \mjolnir\access\Controller_Access;

class Controller_AccessTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\Controller_Access'));
	}

	// @todo tests for \mjolnir\access\Controller_Access

} # test
