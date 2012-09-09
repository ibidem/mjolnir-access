<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Schematic
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Schematic_Ibidem_Access_Base extends \app\Schematic_Base
{
	function down()
	{
		\app\Schematic::destroy
			(
				\app\Model_User::table(), 
				\app\Model_Role::table(), 
				\app\Model_User::assoc_roles(),
				\app\Model_ProfileField::table(),
				\app\Model_ProfileField::assoc_user()
			);
	}
	
	function up()
	{
		\app\Schematic::table
			(
				\app\Model_User::table(), 
				'
					`id`          :key_primary,
					`nickname`    :username,
					`email`       :email,
					`ipaddress`   :ipaddress,
					`pwdverifier` :secure_hash DEFAULT NULL,
					`pwdsalt`     :secure_hash DEFAULT NULL,
					`pwddate`     :datetime_optional DEFAULT NULL,
					`provider`    :titlename DEFAULT NULL,
					`timestamp`   :timestamp,
					
					PRIMARY KEY (`id`)
				'
			);
		
		\app\Schematic::table
			(
				\app\Model_Role::table(), 
				'
					`id`    :key_primary,
					`title` :title NOT NULL,
					
					PRIMARY KEY (`id`)
				'
			);
		
		\app\Schematic::table
			(
				\app\Model_User::assoc_roles(),
				'
					`user` :key_foreign NOT NULL,
					`role` :key_foreign NOT NULL,
					
					KEY `user` (`user`)
				'
			);
		
		\app\Schematic::table
			(
				\app\Model_ProfileField::table(), 
				'
					`id`       :key_primary,
					`idx`      :counter DEFAULT 10,
					`name`     :title NOT NULL,
					`title`    :title NOT NULL,
					`type`     :title NOT NULL,
					`required` :boolean DEFAULT 0,
					
					PRIMARY KEY (`id`)
				'
			);
		
		\app\Schematic::table
			(
				\app\Model_ProfileField::assoc_user(), 
				'
					`user`  :key_foreign NOT NULL,
					`field` :key_foreign NOT NULL,
					`value` :block,
					
					KEY `user` (`user`,`field`),
					KEY `role` (`field`)
				'
			);
	}
	
	function bind()
	{
		\app\Schematic::constraints
			(
				[
					\app\Model_User::assoc_roles() => array
						(
							'user' => array(\app\Model_User::table(), 'CASCADE', 'CASCADE'),
							'role' => array(\app\Model_Role::table(), 'CASCADE', 'CASCADE'),
						),
					\app\Model_ProfileField::assoc_user() => array
						(
							'field' => array(\app\Model_ProfileField::table(), 'CASCADE', 'CASCADE'),
							'user' => array(\app\Model_User::table(), 'CASCADE', 'CASCADE'),
						)
				]
			);
	}
	
	function build()
	{
		// inject roles
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
						INSERT INTO `'.\app\Model_Role::table().'`
							(id, title) VALUES (:id, :title)
					',
					'mysql'
				)
				->bind_int(':id', $id)
				->bind(':title', $title);
			
			\app\SQL::begin();
			try
			{
				foreach ($roles as $desired_title => $desired_id)
				{
					$title = $desired_title;
					$id = $desired_id;
					$statement->execute();
				}
				
				\app\SQL::commit();
			}
			catch(\Exception $e)
			{
				\app\SQL::rollback();
				throw $e;
			}
		}
	}

} # class
