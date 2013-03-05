<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Access
 * @author     Ibidem Team
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Protocol extends \app\Instantiatable
{
	const Everybody  = null;
	const OnlyOwner  = true;
	const OnlyOthers = false;

	/**
	 * @var array
	 */
	protected $relays;

	/**
	 * @var array
	 */
	protected $attributes;

	/**
	 * @var array
	 */
	protected $parameters;

	/**
	 * @var boolean
	 */
	protected $all_parameters = false;

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
	protected $self = null;

	/**
	 * @return static $this
	 */
	function relays(array $relays)
	{
		$this->relays = $relays;
		return $this;
	}

	/**
	 * @return static $this
	 */
	function attr(array $attributes)
	{
		$this->attributes = $attributes;
		return $this;
	}

	/**
	 * Constraints rule to only users who are NOT the owners of said object.
	 *
	 * @return static $this
	 */
	function only_others()
	{
		$this->self = false;
		return $this;
	}

	/**
	 * Constraints rule to only users who are the owners of said object.
	 *
	 * @return static $this
	 */
	function only_owner()
	{
		$this->self = true;
		return $this;
	}

	/**
	 * Resets constraint on ownership back to everybody.
	 *
	 * @return static $this
	 */
	function everybody()
	{
		$this->self = null;
		return $this;
	}

	/**
	 * @return boolean|null
	 */
	function control()
	{
		return $this->self;
	}

	/**
	 * @return static $this
	 */
	function allow($name, array $values)
	{
		$this->parameters or $this->parameters = array();
		$this->parameters[$name] = $values;
		return $this;
	}

	/**
	 * Grant unrestricted access to the given relays. ie. all parameters are
	 * allowed.
	 *
	 * @return static $this
	 */
	function unrestricted()
	{
		$this->all_parameters = true;
		return $this;
	}

	/**
	 * Relays are relays or routes, context is an array of values required for
	 * the match, among these values "owner" is a special idenfication value.
	 *
	 * For inpage elements you must provide attribute restrictions. An attribute
	 * is an element on the page.
	 *
	 * @return boolean
	 */
	function matches($relay, array $context = null, $attribute = null)
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
				else if ( ! empty($context) && ! $this->all_parameters)
				{
					// context requires paramters but rights only give access
					// to non-paramters; the resolution is that it's not allowed
					// unless the all_parameters flag was passed when creating
					// the protocol
					return false;
				}

				// every paramter matched to at least one value. Now we check if
				// we need a object check
				if ($attribute !== null)
				{
					$match = false;
					// if we do we go though all objects and check with the
					// test object

					// do we actually have anything to test?
					if ($this->attributes === null)
					{
						return false;
					}

					foreach ($this->attributes as $attr)
					{
						if ($attr === $attribute)
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
