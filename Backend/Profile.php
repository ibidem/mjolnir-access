<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Backend
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Backend_Profile extends \app\Backend_Collection
{
	protected $model = 'Profile';
	protected $index = 'user-profile-index';

	function fieldtypes()
	{
		return \app\Collection::mirror
			(
				\array_keys(\app\CFS::config('ibidem/profile-fieldtypes'))
			);
	}
	
	
	
} # class
