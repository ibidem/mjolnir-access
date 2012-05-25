<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Security
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Migration_User extends \app\Migration_Template_MySQL
{
	/**
	 * Perform post migration binding operations between tables. 
	 */
	public function bind()
	{
		$this->constraints
			(
				\app\Model_DB_User::assoc_roles(),
				array
				(
					'user' => array(\app\Model_DB_User::table(), 'CASCADE', 'CASCADE'),
					'role' => array(\app\Model_DB_User::roles_table(), 'CASCADE', 'CASCADE'),
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
				\app\Model_DB_User::table(), 
				"
					`id`           :key_primary,
					`nickname`     :username,
					`given_name`   :name,
					`family_name`  :name,
					`email`        :email,
					`ipaddress`    :ipaddress,
					`passwordverifier` :secure_hash,
					`passwordsalt` :secure_hash,
					`passworddate` :datetime_required,
					`datetime`     :timestamp,
					`deleted`      :boolean DEFAULT FALSE,
					
					PRIMARY KEY (`id`)
				"
			);
		
		$this->createtable
			(
				\app\Model_DB_User::roles_table(), 
				"
					`id`    :key_primary,
					`title` :title NOT NULL,
					
					PRIMARY KEY (`id`)
				"
			);
		
		$this->createtable
			(
				\app\Model_DB_User::assoc_roles(),
				"
					`user` :key_foreign NOT NULL,
					`role` :key_foreign NOT NULL,
					
					KEY `user` (`user`,`role`),
					KEY `role` (`role`)
				"
			);
		
		$access_config = \app\CFS::config('ibidem/access');
		$roles = $access_config['roles'];
		if ( ! empty($roles))
		{
			$id = null;
			$title = null;
			$statement = \app\SQL::prepare
				(
					__METHOD__,
					'
						INSERT INTO `'.\app\Model_DB_User::roles_table().'`
							(id, title) VALUES (:id, :title)
					',
					'mysql'
				)
				->bind_int(':id', $id)
				->bind(':title', $title);
			
			foreach ($roles as $desired_title => $desired_id)
			{
			
				$title = $desired_title;
				$id = $desired_id;
				$statement->execute();
			}
		}
		
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
					\app\Model_DB_User::table(), 
					\app\Model_DB_User::roles_table(), 
					\app\Model_DB_User::assoc_roles()
				)
			);
	}

} # class