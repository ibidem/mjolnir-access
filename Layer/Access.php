<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Access
 * @author     Ibidem Team
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Layer_Access extends \app\Instantiatable implements \mjolnir\types\Layer
{
	use \app\Trait_Layer;

	/**
	 * Execute the layer.
	 */
	function run()
	{
		$channel = $this->channel();

		// setup security protocols
		\app\Access::protocols(\app\CFS::config('mjolnir/access'));

		// we register ourselves in the channel
		$channel->set('layer:access', $this);

		// build context
		$relaynode = $channel->get('relaynode');
		$context = $relaynode->get('matcher')->context();
		$relay_context = $relaynode->get('context', null);
		if ($relay_context !== null && \is_array($relaynode->get('context', null)))
		{
			$context = \array_merge($context, $relaynode->get('context'));
		}

		if ( ! \app\Access::can($relaynode->get('target'), $context))
		{
			$http_layer = $channel->get('layer:http');

			// check if this is a http request
			if ($http_layer)
			{
				// redirect to the access route
				\app\Server::redirect(\app\CFS::config('mjolnir/a12n')['default.signin']);
			}

			// else; or if the redirect fails
			throw new \app\Exception_NotAllowed('Acceess denied.');
		}
	}

} # class
