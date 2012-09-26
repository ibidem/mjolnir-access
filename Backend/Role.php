<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Backend
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Backend_Role extends \app\Backend_Collection
{
	protected $model = 'Role';
	protected $index = 'user-role-index';

} # class
