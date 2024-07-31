<?php

function plugin_init_gitlabintegration()
{

	global $PLUGIN_HOOKS, $CFG_GLPI;

	include_once(GLPI_ROOT . "/plugins/gitlabintegration/inc/itemform.class.php");
	include_once(GLPI_ROOT . "/plugins/gitlabintegration/inc/eventlog.class.php");
	include_once(GLPI_ROOT . "/plugins/gitlabintegration/inc/parameters.class.php");
	include_once(GLPI_ROOT . "/plugins/gitlabintegration/inc/gitlabintegration.class.php");
	include_once(GLPI_ROOT . "/plugins/gitlabintegration/inc/permissionsMenu.class.php");
	include_once(GLPI_ROOT . "/plugins/gitlabintegration/inc/categoriesProjectsMenu.class.php");
	include_once(GLPI_ROOT . "/plugins/gitlabintegration/inc/defaultProjectMenu.class.php");
	include_once(GLPI_ROOT . "/plugins/gitlabintegration/inc/profiles.class.php");
	include_once(GLPI_ROOT . "/plugins/gitlabintegration/inc/categoriesProjects.class.php");
	include_once(GLPI_ROOT . "/plugins/gitlabintegration/inc/defaultProject.class.php");

	$PLUGIN_HOOKS['add_css']['gitlabintegration'][] = "css/styles.css";
	$PLUGIN_HOOKS['add_javascript']['gitlabintegration'][] = 'js/buttonsFunctions.js';

	// CSRF compliance : All actions must be done via POST and forms closed by Html::closeForm();
	$PLUGIN_HOOKS['csrf_compliant']['gitlabintegration'] = true;

	if (class_exists('PluginGitlabIntegrationItemForm')) {
		$PLUGIN_HOOKS['post_item_form']['gitlabintegration'] = ['PluginGitlabIntegrationItemForm', 'postItemForm'];
	}

	// add entry to configuration menu
	$PLUGIN_HOOKS['menu_toadd']['gitlabintegration']['admin'] = ['PluginGitlabIntegrationPermissionsMenu', 'PluginGitlabIntegrationCategoriesProjectsMenu', 'PluginGitlabIntegrationDefaultProjectMenu'];
}


function plugin_version_gitlabintegration()
{
	global $DB, $LANG;

	return array(
		'name'			  => __('Gitlab Integration', 'gitlabintegration'),
		'version' 		  => '1.0',
		'author'		  => 'Zohair LAABANE TLEMCANI',
		'license'		  => 'GPLv3+',
		'homepage'		  => '',
		'minGlpiVersion' => '10'
	);
}


function plugin_gitlabintegration_check_prerequisites()
{
	if (GLPI_VERSION >= 10) {
		return true;
	} else {
		echo "GLPI version NOT compatible. Requires GLPI >= 10";
	}
}


function plugin_gitlabintegration_check_config($verbose = false)
{
	if ($verbose) {
		echo 'Installed / not configured';
	}
	return true;
}
