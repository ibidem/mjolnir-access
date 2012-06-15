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
		
		if ($relay['target'] === null)
		{
			$this->body
				(
					\app\ThemeView::instance()
						->theme('ibidem/access')
						->style('default')
						->target('access/signin')
						->layer($this->layer)
						->context($relay['context']::instance())
						->control($relay['control']::instance())
						->render()
				);
		}
		else # target provided
		{
			$this->body
				(
					\app\ThemeView::instance()
						->target($relay['target'])
						->layer($this->layer)
						->context($relay['context']::instance())
						->control($relay['control']::instance())
						->render()
				);
		}
	}
	
	/**
	 * @throws \app\Exception_NotAllowed 
	 */
	public function action_signin()
	{
		$user = \app\Model_DB_User::signin_check($_POST);
		
		if (\app\Layer_HTTP::request_method() === \ibidem\types\HTTP::POST)
		{
			if ($user !== null)
			{
				// logged in
				\app\A12n::signin($user, \app\Model_DB_User::user_role($user));
				
				// redirect
				$base_config = \app\CFS::config('ibidem/base');
				if (isset($base_config['frontend']))
				{
					\app\Layer_HTTP::redirect($base_config['frontend'][0], $base_config['frontend'][1]);
				}

				// no default frontend; we display the checkin page; which now 
				// will show the user's credentials.
				$this->action_index();
			}
			else # signin failed
			{
				$relay = $this->layer->get_relay();
				
				$errors = array
					(
						'ibidem\a12n\signin' => array('form' => array('Sign in failed. Please check your credentials or try a different password.'))
					);
				
				if ($relay['target'] === null)
				{
					$view = \app\ThemeView::instance()
						->theme('ibidem/access')
						->style('default')
						->target('access/signin')
						->errors($errors)
						->layer($this->layer)
						->context($relay['context']::instance())
						->control($relay['control']::instance());
				}
				else # target provided
				{
					$view = \app\ThemeView::instance()
						->target($relay['target'])
						->errors($errors)
						->layer($this->layer)
						->context($relay['context']::instance())
						->control($relay['control']::instance());
				}
				
				$this->body($view->render());
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
	
	public function action_signout()
	{
		\app\A12n::signout();
		\app\Layer_HTTP::redirect('\ibidem\access\a12n', array('action' => 'signin'));
	}

} # class
