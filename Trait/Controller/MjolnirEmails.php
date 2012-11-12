<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Trait
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
trait Trait_Controller_MjolnirEmails
{
	/**
	 * @return array user
	 */
	function mainemail()
	{
		return \app\Model_User::entry(\app\Auth::id())['email'];
	}
	
	/**
	 * @return array emails
	 */
	function secondaryemails()
	{
		return [];
	}
	
	/**
	 * Action: Reset user's password
	 */
	function action_emails()
	{
		if (\app\Auth::role() === \app\A12n::guest())
		{
			throw new \app\Exception_NotAllowed
				('Page is only available when you are signed in.');
		}
		
		if (\app\Server::request_method() === 'POST')
		{
			if ( ! isset($_POST['action']))
			{
				throw new \Exception('Undefined action when using emails manager.');
			}
			
			if ($_POST['action'] === 'add-secondary-email')
			{
				$this->add_secondary_email();
			}
			else if ($_POST['action'] === 'change-main-email')
			{
				$this->change_main_email();
			}
			else # unknown action
			{
				throw new \Exception('Undefined action when using emails manager: '.$_POST['action']);
			}
		}
		else # treat as GET
		{
			$this->emails_view();
		}
	}
	
	function add_secondary_email()
	{
		
	}
	
	function change_main_email($code)
	{
		$token = \app\Model_User::token(\app\Auth::id());
		
		$change_email_url = \app\CFS::config('mjolnir/a12n')['default.emails_manager'].'?action=change_email&code='.$token.'&user='.\app\Auth::id();
	}

} # trait
