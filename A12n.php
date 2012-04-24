<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Security
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class A12n extends \app\Instantiatable
	implements \ibidem\types\Auth
{
	/**
	 * @return int 
	 */
	public function id()
	{
		// @todo \ibidem\access\A12n::id
		return null;
	}
	
	/**
	 * @return string 
	 */
	public function role()
	{
		// @todo \ibidem\access\A12n::role
		return null;
	}
	
	public function current()
	{
		static $current = null;
		
		if (($id = $this->id()) === null)
		{
			return null;
		}
		else # actual id provided
		{	
			if ($current === null)
			{
				$current = \app\SQL::prepare
					(
						'ibidem\access\a12n:current',
						'
							SELECT * 
							  FROM `'.\app\Model_HTTP_User::table().'`
							 WHERE id = :id
						',
						'mysql'
					)
					->setInt(':id', $id)
					->execute()
					->fetch_array();
			}
			
			return $current;
		}
	}
	
	/**
	 * Retrieves the role name for the abstraction notion of "everybody"
	 * 
	 * ie. unauthentificated (such as guests and otherwise)
	 * 
	 * @return string
	 */
	public static function guest()
	{
		return '\ibidem\access\A12n::guest';
	}

} # class
