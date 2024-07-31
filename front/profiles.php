<?php
include( "../../../inc/includes.php");

$criteria = (isset($_GET['criteria'])) ? $_GET['criteria'] : '';
$start = (isset($_GET['start'])) ? $_GET['start'] : 0;

Session::checkLoginUser();

Html::header(
    PluginGitlabIntegrationProfiles::getTypeName(),
    $_SERVER['PHP_SELF'],
    "admin",
    "PluginGitlabIntegrationPermissionsMenu",
    "profiles"
);
PluginGitlabIntegrationProfiles::title();
PluginGitlabIntegrationProfiles::forceTable("glpi_plugin_gitlab_profiles_users");
Search::show('PluginGitlabIntegrationProfiles');
PluginGitlabIntegrationProfiles::configPage($start);
PluginGitlabIntegrationProfiles::massiveActions($start);
PluginGitlabIntegrationProfiles::configPage($start);

Html::footer();

PluginGitlabIntegrationProfiles::dialogActions();
