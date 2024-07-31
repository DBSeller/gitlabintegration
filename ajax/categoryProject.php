<?php

include("../../../inc/includes.php");

Session::checkLoginUser();

// Modo: 1 = associate Project 0 = Delete association
$project_id = $_POST['project_id'];
$category_id = $_POST['category_id'];
$project_name = project($project_id);
$modo = $_POST['modo'];

function project($project_id)
{


    $varIni = parse_ini_file(GLPI_ROOT."/plugins/gitlabintegration/gitlabintegration.ini");

    $url = $varIni["GITLAB_URL"]."/api//v4/projects/".$project_id;
    $headers = array(
        "PRIVATE-TOKEN: ".$varIni["GITLAB_TOKEN"]
    );

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    $result = json_decode($result);
    curl_close($curl);

    return $result->name_with_namespace;
};
//INSERT
if ($modo == 1) {

    $result = $DB->request('glpi_plugin_gitlab_projects', ['OR' => ['project_id' => $project_id, 'category_id' => $category_id]]);
    if ($result->count() > 0) {
        Session::addMessageAfterRedirect(__("The selected Project or Category is already associated", 'gitlabintegration'));
    } else {
        $DB->insert(
            'glpi_plugin_gitlab_projects',
            [
                'project_id'    => $project_id,
                'project_name'  => $project_name,
                'category_id'  => $category_id,
                'created_at' => $_SESSION["glpi_currenttime"]
            ]
        );

        Session::addMessageAfterRedirect(__("The $project_name is associated", 'gitlabintegration'));
    }
}

//DELETE

if ($modo == 0) {
    $result = $DB->request('glpi_plugin_gitlab_projects', ['project_id' => $project_id, 'category_id' => $category_id]);
    if ($result->count() > 0) {
        $DB->delete(
            'glpi_plugin_gitlab_projects',
            [
                'project_id' => $project_id,
                'category_id' => $category_id
            ]
        );

        PluginGitlabIntegrationEventLog::Log($id, 'profiles', $_SESSION["glpi_currenttime"], 'gitlab', 4, sprintf(__('%2s removed permission for id ' . $id, 'gitlabintegration'), $_SESSION["glpiname"]));

        Session::addMessageAfterRedirect(__('Association removed successfully!', 'gitlabintegration'));
    } else {
        $erro = "[" . $_SESSION["glpi_currenttime"] . "] glpiphplog.ERROR: PluginGitlabIntegrationProfiles::permissions() in profile.php line 10" . PHP_EOL;
        $erro = $erro . "  ***PHP Notice: The selected Project can't be deleted: Id: " . $id;
        PluginGitlabIntegrationEventLog::ErrorLog($erro);

        Session::addMessageAfterRedirect(__('The selected profile can not be deleted. Verify logs for more information!'));
    }
}

// modo 2 insert a default Project:
// Modo 3 remove default Project:
// set Default Project

if ($modo == 2) {
    $result = $DB->request("SELECT * FROM glpi_plugin_gitlab_projects WHERE general = 1 OR project_id = $project_id");
    $project = $result->next();
    if ($result->count() > 0) {
        Session::addMessageAfterRedirect(__("a default Project already exists or The $project_name is associated with a category", 'gitlabintegration'));
    } else {
        $project_name = project($project_id);
        $DB->insert(
            'glpi_plugin_gitlab_projects',
            [
                'project_id'    => $project_id,
                'project_name'  => $project_name,
                'general'       => 1,
                'created_at' => $_SESSION["glpi_currenttime"]
            ]
        );

        Session::addMessageAfterRedirect(__("The $defaultProjectName is set as Default Project", 'gitlabintegration'));
    }
}

if ($modo == 3) {
    $result = $DB->request('glpi_plugin_gitlab_projects', ['project_id' => $project_id]);
    echo $result->count();
    if ($result->count() > 0) {
        $DB->delete(
            'glpi_plugin_gitlab_projects',
            [
                'project_id' => $project_id,
                'general' => 1
            ]
        );

        PluginGitlabIntegrationEventLog::Log($id, 'profiles', $_SESSION["glpi_currenttime"], 'gitlab', 4, sprintf(__('%2s removed permission for id ' . $id, 'gitlabintegration'), $_SESSION["glpiname"]));

        Session::addMessageAfterRedirect(__('The Project was removed as default!', 'gitlabintegration'));
    }
}
