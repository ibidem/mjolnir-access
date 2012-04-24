<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Security
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Controller_A12n extends \app\Controller_HTTP
{
	public function action_index()
	{
		$relay = $this->layer->get_relay();
		
		$this->body
			(
				\app\ThemeView::instance()
					->target($relay['target'])
					->layer($this->layer)
					->context($relay['context']::instance()->auth(A12n::instance()))
					->control($relay['control']::instance())
					->render()
			);
	}
	
	/**
	 * @throws \app\Exception_NotAllowed 
	 */
	public function action_signin()
	{
		$user = \app\Model_HTTP_User::signin_check($_POST);
		
		if (\app\Layer_HTTP::request_method() === \ibidem\types\HTTP::POST)
		{
			if ($user !== null)
			{
				// logged in
				\app\Session::set('user', $user);
				\app\Session::set('role', \app\Model_HTTP_User::user_role($user));
				// redirect
				$base_config = \app\CFS::config('ibidem/base');
				if (isset($base_config['frontend']))
				{
					\app\Layer_HTTP::redirect($base_config['frontend'][0], $base_config['frontend'][1]);
				}

				// no default frontend; we display the checkin page; which now 
				// will show the user's credentials.
				// $this->action_index();
				echo 'success';
			}
			else # signin failed
			{
				$this->body('failed to signin');
			}
		}
		else if (\app\Layer_HTTP::request_method() === \ibidem\types\HTTP::GET)
		{
			$this->action_index();
		}
		else # not allowed 
		{
			throw new \app\Exception_NotAllowed
				('Forbidden request method.');
		}
	}

} # class
