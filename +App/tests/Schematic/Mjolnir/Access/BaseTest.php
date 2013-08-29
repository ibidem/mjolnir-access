<?php namespace mjolnir\access\tests;

use \mjolnir\access\Schematic_Mjolnir_Access_Base;

class Schematic_Mjolnir_Access_BaseTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\Schematic_Mjolnir_Access_Base'));
	}

	// @todo tests for \mjolnir\access\Schematic_Mjolnir_Access_Base

} # test
