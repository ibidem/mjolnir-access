<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Security
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Layer_Access extends \app\Layer
{
	/**
	 * @var string 
	 */
	private $target;
	
	/**
	 * @var array
	 */
	private $relay;
	
	/**
	 * @return \ibidem\access\Layer_Access $this
	 */
	public static function instance()
	{
		$instance = parent::instance();
		// setup security protocols
		\app\Access::protocols(\app\CFS::config('ibidem/access'));
		
		return $instance;
	}
	
	/**
	 * @param \Exception exception
	 * @param boolean $origin 
	 */
	public function exception(\Exception $exception, $origin = false)
	{
		if (\is_a($exception, '\\ibidem\\types\\Exception'))
		{
			if ($exception->get_type() === \ibidem\types\Exception::NotAllowed)
			{
				$this->contents(null);
				$layer = $this->find('http');
				if ($layer !== null)
				{
					$layer->status(\ibidem\types\HTTP::STATUS_Forbidden);
				}
			}
		}
		
		// default execution from Layer
		parent::exception($exception);
	}
	
	/**
	 * Execute the layer.
	 */
	public function execute()
	{
		try 
		{
			// build context
			$context = $this->relay['route']->get_context();
			if (isset($this->relay['context']))
			{
				$context = \array_merge($context, $this->relay['context']);
			}
			
			if ( ! \app\Access::can($this->target, $context))
			{
				throw new \app\Exception_NotAllowed
					('Acceess denied.');
			}
			
			// continue execution
			parent::execute();
			
			if ($this->layer)
			{
				$this->contents($this->layer->get_contents());
			}
		}
		catch (\Exception $e)
		{
			$this->exception($e);
		}
	}
	
	/**
	 * @param array relay
	 * @return \ibidem\access\Layer_Access $this
	 */
	public function relay($relay)
	{
		$this->relay = $relay;
		return $this;
	}
	
	/**
	 * @param string context
	 * @return \ibidem\access\Layer_Access $this
	 */
	public function target($target)
	{
		$this->target = $target;
		return $this;
	}
	
} # class
