<?php return array
	(
		'\ibidem\access\a12n' => array
			(
				'route' => \app\Route_Pattern::instance()
					->standard
						(
							'access(/<action>)', 
							[
								'action' => '(signin|signout|signup)'
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
	
		'\ibidem\access\channel' => array
			(
				'route' => \app\Route_Pattern::instance()
					->standard
						(
							'access/provider(/<provider>)', 
							[
								'action' => '[a-zA-Z0-9]+'
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
