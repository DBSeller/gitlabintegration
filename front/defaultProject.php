<?php
include("../../../inc/includes.php");

// $criteria = $_GET['criteria'];
$start = (isset($_GET['start'])) ? $_GET['start'] : 0;

Session::checkLoginUser();

Html::header(
    PluginGitlabIntegrationDefaultProject::getTypeName(),
    $_SERVER['PHP_SELF'],
    "admin",
    "PluginGitlabIntegrationDefaultProjectMenu",
    "defaultProject"
);
PluginGitlabIntegrationDefaultProject::title();
PluginGitlabIntegrationDefaultProject::forceTable("glpi_plugin_gitlab_projects");
// Search::show('PluginGitlabIntegrationDefaultProject');
PluginGitlabIntegrationDefaultProject::configPage($start);
PluginGitlabIntegrationDefaultProject::massiveActions($start);
PluginGitlabIntegrationDefaultProject::configPage($start);

Html::footer();

//PluginGitlabIntegrationDefaultProject::dialogActions();
