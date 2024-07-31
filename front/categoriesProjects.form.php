<?php
include( "../../../inc/includes.php");

$criteria = (isset($_GET['criteria'])) ? $_GET['criteria'] : '';
$start = (isset($_GET['start'])) ? $_GET['start'] : 0;

Session::checkLoginUser();

Html::header(
    PluginGitlabIntegrationCategoriesProjects::getTypeName(),
    $_SERVER['PHP_SELF'],
    "admin",
    "PluginGitlabIntegrationCategoriesProjectsMenu",
    "categoriesProjects"
);

(new PluginGitlabIntegrationCategoriesProjects)->showForm(0, []);

Html::footer();
