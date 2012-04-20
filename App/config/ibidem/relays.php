<?php return array
	(
		'\ibidem\access\A12n' => array
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
				'enabled' => true,
			// MVC
				'controller' => '\ibidem\access\Controller_A12n',
				'action' => 'index',
				'target' => null, # theme targeting
			),
	);
