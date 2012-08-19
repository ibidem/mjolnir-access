<?php return array
	(
		'\ibidem\access\channel' => array
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
				'controller' => '\ibidem\access\Controller_Access',
				'action'  => 'action_channel',
				'target'  => null, # theme targeting
				'control' => '\ibidem\access\Controller_Access',
				'context' => '\ibidem\access\Context_Access',
			),
	
		'\ibidem\access\endpoint' => array
			(
				'matcher' => \app\Route_Pattern::instance()
					->standard('access/channel-endpoint', []),
				'enabled' => false,
			// MVC
				'controller' => '\ibidem\access\Controller_Access',
				'action'  => 'action_endpoint',
				'target'  => null, # theme targeting
				'control' => '\ibidem\access\Controller_Access',
				'context' => '\ibidem\access\Context_Access',
			),
	
		'\ibidem\access\a12n' => array
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
				'controller' => '\ibidem\access\Controller_A12n',
				'action'  => 'action_index',
				'target'  => null, # theme targeting
				'control' => '\ibidem\access\Controller_A12n',
				'context' => '\ibidem\access\Context_Access',
			),
	);
