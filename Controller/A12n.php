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

} # class
