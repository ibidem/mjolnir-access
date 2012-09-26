<?php namespace mjolnir\access;

$mvc_stack = function ($relay, $target)
	{
		\app\Layer::stack
			(
				\app\Layer_Access::instance()
					->relay_config($relay)
					->target($target),
				\app\Layer_HTTP::instance(),
				\app\Layer_HTML::instance(),
				\app\Layer_MVC::instance()
					->relay_config($relay)
			);
	};

\app\Relay::process('\mjolnir\access\channel', $mvc_stack);
\app\Relay::process('\mjolnir\access\endpoint', $mvc_stack);
\app\Relay::process('\mjolnir\access\a12n', $mvc_stack);
