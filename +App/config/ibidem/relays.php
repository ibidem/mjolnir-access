<?php return array
	(
		'\ibidem\access\a12n' => array
			(
				'route' => \app\Route_Pattern::instance()
					->standard
						(
							'access(/<action>)', 
							array
							(
								'action' => '(signin|signout|signup)'
							)
						),
				'enabled' => false,
			// MVC
				'controller' => '\ibidem\access\Controller_A12n',
				'action'  => 'action_index',
				'target'  => null, # theme targeting
				'control' => '\ibidem\access\Controller_A12n',
				'context' => '\ibidem\access\Context_Access',
			),
	
		'\ibidem\access\openid' => array
			(
				'route' => \app\Route_Pattern::instance()
					->standard
						(
							'access/openid(/<openid>)', 
							array
							(
								'openid' => '[a-z9-0-]+'
							)
						),
				'enabled' => false,
			// MVC
				'controller' => '\ibidem\access\Controller_A12n',
				'action'  => 'action_openid',
				'target'  => null, # theme targeting
				'control' => '\ibidem\access\Controller_A12n',
				'context' => '\ibidem\access\Context_Access',
			),
	);
