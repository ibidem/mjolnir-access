<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Security
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Controller_A12n extends \app\Controller_Web
{
	protected static $target = null;

	function before()
	{		
		\app\GlobalEvent::fire('webpage:title', 'Access');
	}
	
	function action_index()
	{
		$relay = $this->layer->get_relay();
		
		if (\app\A12n::instance()->role() === \app\A12n::guest())
		{
			\app\Server::redirect(\app\URL::href('\ibidem\access\a12n', ['action' => 'signin']));
		}
		
		\app\GlobalEvent::fire('webpage:title', 'Lobby · Access');
		
		$this->body
			(
				\app\ThemeView::instance()
					->theme('ibidem/access')
					->style('default')
					->target('lobby')
					->layer($this->layer)
					->context($relay['context']::instance())
					->control($relay['control']::instance())
					->render()
			);
	}
	
	function signin_view()
	{
		$relay = $this->layer->get_relay();
		
		if ($relay['target'] === null)
		{	
			\app\GlobalEvent::fire('webpage:title', 'Sign In · Access');
			
			$this->body
				(
					\app\ThemeView::instance()
						->theme('ibidem/access')
						->style('default')
						->target('signin')
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
	function action_signin()
	{
		if (\app\A12n::instance()->role() !== \app\A12n::guest())
		{
			\app\Server::redirect(\app\URL::href('\ibidem\access\a12n', ['action' => 'index']));
		}
		
		if (\app\Server::request_method() === 'POST')
		{
			$user = \app\Model_User::signin_check($_POST);
			
			if ($user !== null)
			{
				// logged in
				\app\A12n::signin($user, \app\Model_User::role_for($user));
				
				// redirect
				$base_config = \app\CFS::config('ibidem/base');
				if (isset($base_config['site:frontend']))
				{
					\app\Server::redirect
						(
							'//'.$base_config['domain'].$base_config['path'].$base_config['site:frontend']
						);
				}

				// no default frontend
				\app\Server::redirect(\app\URL::href('\ibidem\access\a12n', ['action' => 'lobby']));
			}
			else # signin failed
			{
				$relay = $this->layer->get_relay();
				
				$errors = array
					(
						'ibidem\a12n\signin' => array
							(
								'form' => ['Sign in failed. Please check your credentials or try a different password.']
							)
					);
			}
		}
		
		$this->signin_view();
	}
	
	function action_signout()
	{
		\app\A12n::signout();
		\app\Server::redirect(\app\URL::href('\ibidem\access\a12n', ['action' => 'signin']));
	}


} # class
