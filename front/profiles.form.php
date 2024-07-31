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

(new PluginGitlabIntegrationProfiles)->showForm(0, []);

Html::footer();
