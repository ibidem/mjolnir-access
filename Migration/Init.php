<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Security
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Migration_Init extends \app\Migration_Template_MySQL
{
	/**
	 * Perform post migration binding operations between tables. 
	 */
	public function bind()
	{
		$this->constraints
			(
				\app\Model_HTTP_User::user_role_table(),
				array
				(
					'user' => array(\app\Model_HTTP_User::table(), 'CASCADE', 'CASCADE'),
					'role' => array(\app\Model_HTTP_User::roles_table(), 'CASCADE', 'CASCADE'),
				)
			);
	}
	
	/**
	 * @return array
	 */
	public function up()
	{
		$this->createtable
			(
				\app\Model_HTTP_User::table(), 
				"
					`id`        :key_primary,
					`email`     :email,
					`nickname`  :username,
					`ipaddress` :ipaddress,
					`passwordverifier` :secure_hash,
					`passwordsalt` :secure_hash,
					`passworddate` :date_required,
					`datetime`     :timestamp,
					
					PRIMARY KEY (`id`)
				"
			);
		
		$this->createtable
			(
				\app\Model_HTTP_User::roles_table(), 
				"
					`id`    :key_primary,
					`title` :title NOT NULL,
					
					PRIMARY KEY (`id`)
				"
			);
		
		$this->createtable
			(
				\app\Model_HTTP_User::user_role_table(),
				"
					`user` :key_foreign DEFAULT NULL,
					`role` :key_foreign NOT NULL,
					
					KEY `user` (`user`,`role`),
					KEY `role` (`role`)
				"
			);
		
		\app\SQL::insert
			(
				'ibidem/access:migration_init_simplerole',
				array
				(
					'title' => 'member',
				),
				\app\Model_HTTP_User::roles_table()
			);
		
		// return a callback to binding
		return $this->bind_callback();
	}
	
	
	/**
	 * Tear down all tables
	 */
	public function down()
	{
		$this->droptables
			(
				array
				(
					\app\Model_HTTP_User::table(), 
					\app\Model_HTTP_User::roles_table(), 
					\app\Model_HTTP_User::user_role_table()
				)
			);
	}

} # class
