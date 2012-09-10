<?php return array
	(
		'table_prefix' => 'ib_',
		'databases' => array
			(
				'default' => array
					(
						'connection' => array
							(
								/**
								 * The following options are available for PDO:
								 *
								 * string   dsn         Data Source Name
								 * string   username    database username
								 * string   password    database password
								 * boolean  persistent  use persistent connections?
								 */
								'dsn'        => 'mysql:host=localhost;dbname=mjolnir',
								'username'   => 'root',
								'password'   => '',
								'persistent' => false,
							),
					),
			),
	);
