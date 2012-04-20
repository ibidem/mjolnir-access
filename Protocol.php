<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Security
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Protocol extends \app\Instantiatable
{
	/**
	 * @var array 
	 */
	private $relays;
	
	/**
	 * @var array
	 */
	private $attributes;
	
	/**
	 * @var array
	 */
	private $parameters;
	
	/**
	 * null is a control value. If the attribute is set to null this means there
	 * is no self constraint in action. Otherwise, if the value is a boolean 
	 * then if the value is true the permission will only apply if the owner in
	 * context is the owner of the object, else if it is false then the 
	 * constraint will only apply if the owner of the object is NOT the user
	 * in question; eg. "+1" feature only applies to everyone that is not the
	 * owner of said object, similarly a "edit" feature automatically applies if
	 * the user trying to edit is the owner of the resource
	 * 
	 * @var boolean|null 
	 */
	private $self = null;
	
	/**
	 * @param string relay
	 * @return \ibidem\access\Protocol_Rule $this
	 */
	public function relays(array $relays)
	{
		$this->relays = $relays;
		return $this;
	}
	
	/**
	 * @param array attributes
	 * @return \ibidem\access\Protocol_Rule $this
	 */
	public function attributes(array $attributes)
	{
		$this->attributes = $attributes;
		return $this;
	}
	
	/**
	 * Constraints rule to only users who are the owners of said object. 
	 * 
	 * @return \ibidem\access\Protocol 
	 */
	public function only_others()
	{
		$this->self = false;
		return $this;
	}
	
	/**
	 * Constraints rule to only users who are NOT the owners of said object.
	 * 
	 * @return \ibidem\access\Protocol $this
	 */
	public function only_owner()
	{
		$this->self = true;
		return $this;
	}
	
	/**
	 * Resets constraint on ownership back to everybody.
	 * 
	 * @return \ibidem\access\Protocol $this
	 */
	public function everybody()
	{
		$this->self = null;
		return $this;
	}
	
	/**
	 * @return boolean|null 
	 */
	public function get_self()
	{
		return $this->self;
	}
	
	/**
	 * @param string name
	 * @param array values
	 * @return \ibidem\access\Protocol $this
	 */
	public function param($name, array $values)
	{
		$this->parameters or $this->parameters = array();
		$this->parameters[$name] = $values;
		return $this;
	}
	
	/**
	 * @param string relay
	 * @param array context
	 * @param string attribute 
	 */
	public function matches($relay, array $context = null, $test_attribute = null)
	{
		// cycle though acceptable relays
		foreach ($this->relays as $name)
		{
			// found a relay
			if ($name === $relay)
			{
				if ($this->parameters !== null)
				{
					// we check if every paramter registered for this permission 
					// definition is set in the current test
					foreach ($this->parameters as $key => $values)
					{
						// we assume it doesn't match; ie. deny by default
						$match = false;
						// we get the value for the paramter definition, or null
						$param_value = isset($context[$key]) ? $context[$key] : null;
						// if we got NULL, it means the value wasn't set so we 
						// fail the test. All paramters in the definition must 
						// match, to at least one allowed value
						if ($param_value !== null)
						{
							// if it matched we go over the values in the 
							// definition
							foreach ($values as $value)
							{
								// ... and check with the value we got
								if ($value === $param_value)
								{
									// we found a match
									$match = true;
									// break out of the values loop, but we 
									// still need to continue check the rest of 
									// the paramters in the definition
									break;
								}
							}
						}
						else # parameter is not set
						{
							// we fail the match
							return false;
						}

						// if we fail to match even 1 paramter then we fail the
						// match for the definition. All paramters must match 
						// with at least one value
						if ($match == false)
						{
							return false;
						}
					}
				}

				// every paramter matched to at least one value. Now we check if 
				// we need a object check
				if ($test_attribute !== null)
				{
					$match = false;
					// if we do we go though all objects
					// and check with the test object
					foreach ($this->attributes as $attribute)
					{
						if ($attribute === $test_attribute)
						{
							// match found
							$match = true;
							// no reason to go on
							break;
						}
					}
					
					// we return the results of our search
					return $match;
				}
				else # no object defintion
				{
					return true;
				}
			}
		}
		
		// the permission doesn't apply to the definition in question
		return false;
	}

} # class
