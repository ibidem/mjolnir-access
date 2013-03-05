<?php return array
	(
		'mjolnir:access/channel.route' => array
			(
				'matcher' => \app\URLRoute::instance()
					->urlpattern
						(
							'access/channel/<provider>(/<id>)',
							[
								'provider' => '[a-zA-Z0-9]+',
								'id' => '[a-zA-Z0-9]+',
							]
						),
				'enabled' => false,
			// MVC
				'controller' => '\mjolnir\access\Controller_Access',
				'action'  => 'channel',
				'target'  => null, # theme targeting
				'control' => '\mjolnir\access\Controller_Access',
				'context' => '\mjolnir\access\Controller_Access',
			),

		'mjolnir:access/endpoint.route' => array
			(
				'matcher' =>\app\URLRoute::instance()
					->urlpattern('access/channel-endpoint'),
				'enabled' => false,
			// MVC
				'controller' => '\mjolnir\access\Controller_Access',
				'action'  => 'endpoint',
				'target'  => null, # theme targeting
				'control' => '\mjolnir\access\Controller_Access',
				'context' => '\mjolnir\access\Controller_Access',
			),

		'mjolnir:access/auth.route' => array
			(
				'matcher' =>\app\URLRoute::instance()
					->urlpattern
						(
							'access(/<action>)',
							[
								'action' => '(signin|signout|signup|lobby|pwdreset|emails|add-email|update-mainemail)'
							]
						),
				'enabled' => false,
			// MVC
				'controller' => '\mjolnir\access\Controller_Access',
				'action'  => 'index',
			),
	);
