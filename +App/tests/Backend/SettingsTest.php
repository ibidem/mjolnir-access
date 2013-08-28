<?php namespace mjolnir\access\tests;

use \mjolnir\access\Backend_Settings;

class Backend_SettingsTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\Backend_Settings'));
	}

	// @todo tests for \mjolnir\access\Backend_Settings

} # test
