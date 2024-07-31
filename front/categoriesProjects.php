<?php
include( "../../../inc/includes.php");

$criteria = (isset($_GET['criteria'])) ? $_GET['criteria'] : '';
$start = (isset($_GET['start'])) ? $_GET['start'] : 0;

// $criteria = $_GET['criteria'];

Session::checkLoginUser();

Html::header(
    PluginGitlabIntegrationCategoriesProjects::getTypeName(),
    $_SERVER['PHP_SELF'],
    "admin",
    "PluginGitlabIntegrationCategoriesProjectsMenu",
    "categoriesProjects"
);
PluginGitlabIntegrationCategoriesProjects::title();
// Search::show('PluginGitlabIntegrationCategoriesProjects');
PluginGitlabIntegrationCategoriesProjects::configPage($start);
PluginGitlabIntegrationCategoriesProjects::massiveActions($start);
PluginGitlabIntegrationCategoriesProjects::configPage($start);

Html::footer();

PluginGitlabIntegrationCategoriesProjects::dialogActions();
