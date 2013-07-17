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

		if ($channel->status() === \app\Channel::error)
		{
			return; # allow all error processing
		}

		// setup security protocols
		\app\Access::protocols(\app\CFS::config('mjolnir/access'));

		// we register ourselves in the channel
		$channel->set('layer:access', $this);

		// build context
		$relaynode = $channel->get('relaynode');

		$relaymatcher = $relaynode->get('matcher', null);

		// we can potentially not have a route matcher when the routing is a
		// hardcoded code path and not actual routing from outside input
		if ($relaymatcher !== null && ! \is_bool($relaymatcher))
		{
			$context = $relaymatcher->context();
		}
		else # no relaymatcher
		{
			$context = [];
		}

		$relay_context = $relaynode->get('context', null);
		if ($relay_context !== null && \is_array($relaynode->get('context', null)))
		{
			$context = \array_merge($context, $relaynode->get('context'));
		}

		if ( ! \app\Access::can($relaynode->get('relaykey'), $context))
		{
			$http_layer = $channel->get('layer:http');

			// check if this is a http request
			if ($http_layer)
			{
				// redirect to the access route
				\app\Server::redirect(\app\CFS::config('mjolnir/auth')['default.signin']);
			}

			// else; or if the redirect fails
			throw new \app\Exception_NotAllowed('Acceess denied.');
		}
	}

} # class
