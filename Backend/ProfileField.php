<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Backend
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Backend_ProfileField extends \app\Backend_Collection
{
	protected $model = 'ProfileField';
	protected $index = 'user-profile-index';

	function fieldtypes()
	{
		return \app\Collection::mirror
			(
				\array_keys(\app\CFS::config('mjolnir/profile-fieldtypes'))
			);
	}
	
	function entries($page, $limit, $offset = 0, array $order = [], array $constraints = [])
	{
		$order['idx'] = 'ASC';
		return parent::entries($page, $limit, $offset, $order, $constraints);
	}
	
} # class
