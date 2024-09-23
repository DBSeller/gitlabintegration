<?php

function plugin_gitlabintegration_install()
{

	global $DB;

	$config = new Config();
	$config->setConfigurationValues('plugin:Gitlab Integration', ['configuration' => false]);

	ProfileRight::addProfileRights(['gitlabintegration:read']);

	//instanciate migration with version
	$migration = new Migration(100);

	// //Create table glpi_plugin_gitlab_integration only if it does not exists yet!
	plugin_gitlabintegration_create_integration($DB);

	//Create table glpi_plugin_gitlab_profiles only if it does not exists yet!
	plugin_gitlabintegration_create_profiles($DB);

	//Create table glpi_plugin_gitlab_categories_projects only if it does not exists yet!
	plugin_gitlabintegration_create_projects($DB);

	//Create table glpi_plugin_gitlab_parameters only if it does not exists yet!
	plugin_gitlabintegration_create_parameters($DB);

	//Insert parameters at table glpi_plugin_gitlab_parameters only if it exist!
	plugin_gitlabintegration_insert_parameters($DB);

	return true;
}

function plugin_gitlabintegration_uninstall()
{

	global $DB;

	$config = new Config();
	$config->deleteConfigurationValues('plugin:Gitlab Integration', ['configuration' => false]);

	ProfileRight::deleteProfileRights(['gitlabintegration:read']);

	$notif = new Notification();
	$options = [
		'itemtype' => 'Ticket',
		'event'    => 'plugin_gitlabintegration',
		'FIELDS'   => 'id'
	];
	foreach ($DB->request('glpi_notifications', $options) as $data) {
		$notif->delete($data);
	}

	//Drop table glpi_plugin_gitlab_integration only if it exists!
	plugin_gitlabintegration_delete_integration($DB);

	//Drop table glpi_plugin_gitlab_profiles_users only if it exists!
	plugin_gitlabintegration_delete_profiles($DB);

	//Drop table glpi_plugin_gitlab_parameters only if it exists!
	plugin_gitlabintegration_delete_parameters($DB);

	//Drop table glpi_plugin_gitlab_projects only if it exists!
	plugin_gitlabintegration_delete_projects($DB);

	return true;
}

function plugin_gitlabintegration_create_integration($DB)
{
	if (!$DB->tableExists('glpi_plugin_gitlab_integration')) {
		$query = "CREATE TABLE  IF NOT EXISTS  `glpi_plugin_gitlab_integration` (
				   `id` INT(11) NOT NULL AUTO_INCREMENT,
				   `ticket_id` INT(11) NOT NULL,
				   `gitlab_project_id` INT(11) NOT NULL,
				   `gitlab_member_id` INT (11),
				   PRIMARY KEY  (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
		$DB->queryOrDie($query, $DB->error());

		$query = "ALTER TABLE `glpi_plugin_gitlab_integration`
	                DROP CONSTRAINT IF EXISTS `fk_gitlab_ticket`";
		$DB->queryOrDie($query, $DB->error());


		$query = "ALTER TABLE `glpi_plugin_gitlab_integration`
	                ADD CONSTRAINT `fk_gitlab_ticket`
					FOREIGN KEY (`ticket_id`) REFERENCES `glpi_tickets` (`id`)";
		$DB->queryOrDie($query, $DB->error());
	}
}

function plugin_gitlabintegration_create_profiles($DB)
{
	if (!$DB->tableExists('glpi_plugin_gitlab_profiles_users')) {
		$query = "CREATE TABLE IF NOT EXISTS `glpi_plugin_gitlab_profiles_users` (
				   `id` INT(11) NOT NULL AUTO_INCREMENT,
				   `profile_id` INT(11) NOT NULL,
				   `user_id` INT(11) NOT NULL,
				   `created_at` DATETIME,
				   PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
		$DB->queryOrDie($query, $DB->error());

		$query = "ALTER TABLE IF NOT EXISTS `glpi_plugin_gitlab_profiles_users`
	                ADD CONSTRAINT `fk_gitlab_profile`
					FOREIGN KEY (`profile_id`) REFERENCES `glpi_profiles` (`id`)";
		$DB->queryOrDie($query, $DB->error());

		$query = "ALTER TABLE `glpi_plugin_gitlab_profiles_users` 
	                ADD CONSTRAINT `fk_gitlab_user` 
					FOREIGN KEY (`user_id`) REFERENCES `glpi_users` (`id`)";
		$DB->queryOrDie($query, $DB->error());
	}
}

function plugin_gitlabintegration_create_projects($DB)
{
	if (!$DB->tableExists('glpi_plugin_gitlab_projects')) {
		$query = "CREATE TABLE IF NOT EXISTS  `glpi_plugin_gitlab_projects` (
				   `id` INT(11) NOT NULL AUTO_INCREMENT,
				   `project_id` INT(11) NOT NULL,
				   `project_name` VARCHAR(255) NOT NULL,
				   `category_id` INT(11) NULL,
				   `general` BOOLEAN NOT NULL DEFAULT 0,
				   `created_at` DATETIME,
				   PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
		$DB->queryOrDie($query, $DB->error());

		$query = "ALTER TABLE `glpi_plugin_gitlab_projects`
	                ADD CONSTRAINT `fk_gitlab_categories`
					FOREIGN KEY (`category_id`) REFERENCES `glpi_itilcategories` (`id`)";
		$DB->queryOrDie($query, $DB->error());
	}
}

function plugin_gitlabintegration_create_parameters($DB)
{
	if (!$DB->tableExists('glpi_plugin_gitlab_parameters')) {
		$query = "CREATE TABLE IF NOT EXISTS  `glpi_plugin_gitlab_parameters` (
				   `id` INT(11) NOT NULL AUTO_INCREMENT,
				   `name` VARCHAR(50) NOT NULL,
				   `value` VARCHAR(125),
				   PRIMARY KEY  (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
		$DB->queryOrDie($query, $DB->error());

		$query = "ALTER TABLE `glpi_plugin_gitlab_parameters`
	                ADD CONSTRAINT `uk_name`
					UNIQUE (`name`) ";
		$DB->queryOrDie($query, $DB->error());
	}
}

function plugin_gitlabintegration_delete_integration($DB)
{
	if ($DB->tableExists('glpi_plugin_gitlab_integration')) {
		$drop_count = "DROP TABLE glpi_plugin_gitlab_integration";
		$DB->query($drop_count);
	}
}

function plugin_gitlabintegration_delete_profiles($DB)
{
	if ($DB->tableExists('glpi_plugin_gitlab_profiles_users')) {
		$drop_count = "DROP TABLE glpi_plugin_gitlab_profiles_users";
		$DB->query($drop_count);
	}
}

function plugin_gitlabintegration_delete_projects($DB)
{
	if ($DB->tableExists('glpi_plugin_gitlab_projects')) {
		$drop_count = "DROP TABLE glpi_plugin_gitlab_projects";
		$DB->query($drop_count);
	}
}

function plugin_gitlabintegration_delete_parameters($DB)
{
	if ($DB->tableExists('glpi_plugin_gitlab_parameters')) {
		$drop_count = "DROP TABLE glpi_plugin_gitlab_parameters";
		$DB->query($drop_count);
	}
}

function plugin_gitlabintegration_insert_parameters($DB)
{
	if ($DB->tableExists('glpi_plugin_gitlab_parameters')) {

		$ini_array = parse_ini_file("gitlabintegration.ini");

		$parameters = [
			[
				'name'  => 'gitlab_url',
				'value' => $ini_array['GITLAB_URL'] == "" ? NULL : $ini_array['GITLAB_URL']
			],
			[
				'name'  => 'gitlab_token',
				'value' => $ini_array['GITLAB_TOKEN'] == "" ? NULL : $ini_array['GITLAB_TOKEN']
			]
		];

		foreach ($parameters as $parameter) {
			$DB->insert(
				'glpi_plugin_gitlab_parameters',
				[
					'name'  => $parameter['name'],
					'value' => $parameter['value']
				]
			);
		}
	}
}
