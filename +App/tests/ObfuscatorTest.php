<?php namespace mjolnir\access\tests;

use \mjolnir\access\Obfuscator;

class ObfuscatorTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\Obfuscator'));
	}

	// @todo tests for \mjolnir\access\Obfuscator

} # test
