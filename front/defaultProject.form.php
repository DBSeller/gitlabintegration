<?php
include( "../../../inc/includes.php");

$criteria = (isset($_GET['criteria'])) ? $_GET['criteria'] : '';
$start = (isset($_GET['start'])) ? $_GET['start'] : 0;
Session::checkLoginUser();

Html::header(
    PluginGitlabIntegrationDefaultProject::getTypeName(),
    $_SERVER['PHP_SELF'],
    "admin",
    "PluginGitlabIntegrationDefaultProjectMenu",
    "defaultProject"
);

 (new PluginGitlabIntegrationDefaultProject)->showForm(1,array());

Html::footer();
