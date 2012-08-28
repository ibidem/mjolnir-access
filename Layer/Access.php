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
	static function instance()
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
	function exception(\Exception $exception, $no_throw = false, $origin = false)
	{
		if (\is_a($exception, '\ibidem\types\Exception'))
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
		parent::exception($exception, $no_throw);
	}
	
	/**
	 * Execute the layer.
	 */
	function execute()
	{
		try 
		{			
			// build context
			$context = $this->relay['matcher']->get_context();
			if (isset($this->relay['context']) && \is_array($this->relay['context']))
			{
				$context = \array_merge($context, $this->relay['context']);
			}
			
			if ( ! \app\Access::can($this->target, $context))
			{
				$http_layer = \app\Layer::find('http');
				if ($http_layer && \app\Access::can('\ibidem\access\a12n', ['action' => 'signin']))
				{
					// redirect to the access route
					\app\Server::redirect(\app\URL::href('\ibidem\access\a12n', ['action' => 'signin']));
				}
				
				// else; or if the redirect fails 
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
	 * @param array relay configuration
	 * @return \ibidem\base\Layer_MVC $this
	 */
	function relay_config(array $relay)
	{
		// [!!] don't do actual configuration here; do it in the execution loop;
		// not only is it potentially unused configuration but when this is 
		// called there is also no gurantee the Layer itself is configured
		$this->relay = $relay;
		return $this;
	}	
	
	/**
	 * @param string context
	 * @return \ibidem\access\Layer_Access $this
	 */
	function target($target)
	{
		$this->target = $target;
		return $this;
	}
	
} # class
