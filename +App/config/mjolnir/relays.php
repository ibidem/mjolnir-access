<?php return array
	(
		'\mjolnir\access\channel' => array
			(
				'matcher' => \app\Route_Pattern::instance()
					->standard
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
				'action'  => 'action_channel',
				'target'  => null, # theme targeting
				'control' => '\mjolnir\access\Controller_Access',
				'context' => '\mjolnir\access\Context_Access',
			),
	
		'\mjolnir\access\endpoint' => array
			(
				'matcher' => \app\Route_Pattern::instance()
					->standard('access/channel-endpoint', []),
				'enabled' => false,
			// MVC
				'controller' => '\mjolnir\access\Controller_Access',
				'action'  => 'action_endpoint',
				'target'  => null, # theme targeting
				'control' => '\mjolnir\access\Controller_Access',
				'context' => '\mjolnir\access\Context_Access',
			),
	
		'\mjolnir\access\a12n' => array
			(
				'matcher' => \app\Route_Pattern::instance()
					->standard
						(
							'access(/<action>)', 
							[
								'action' => '(signin|signout|signup|lobby)'
							]
						),
				'enabled' => false,
			// MVC
				'controller' => '\mjolnir\access\Controller_A12n',
				'action'  => 'action_index',
				'target'  => null, # theme targeting
				'control' => '\mjolnir\access\Controller_A12n',
				'context' => '\mjolnir\access\Context_Access',
			),
	);
