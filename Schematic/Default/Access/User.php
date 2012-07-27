<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Schematic
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Schematic_Default_Access_User extends \app\Schematic_Base
{
	function down()
	{
		\app\Schematic::drop
			(
				\app\Model_DB_User::table(), 
				\app\Model_DB_User::roles_table(), 
				\app\Model_DB_User::assoc_roles()
			);
	}

} # class
