<?php
/*
 -------------------------------------------------------------------------
GLPI - Gestionnaire Libre de Parc Informatique
Copyright (C) 2003-2019 by the INDEPNET Development Team.

http://indepnet.net/   http://glpi-project.org
-------------------------------------------------------------------------

LICENSE

This file is part of GLPI.

GLPI is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

GLPI is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with GLPI. If not, see <http://www.gnu.org/licenses/>.
--------------------------------------------------------------------------
 */

/**
 * Summary of PluginGitlabIntegrationProfiles
 * */

class PluginGitlabIntegrationDefaultProject extends CommonDBTM
{
    static $rightname = 'defaultProject';

    /**
     * Display contents the create of defaultProject Permission.
     *
     * @param void
     *
     * @return boolean with the Permission of update
     */
    static function canCreate()
    {
        return self::canUpdate();
    }

    /**
     * Display contents the title of defaultProject Permission.
     *
     * @param void
     *
     * @return void
     */
    static function title()
    {
        echo '<script type="text/javascript" src="../js/buttonsFunctions.js"></script>';
        echo "<table class='tab_glpi'><tbody>";
        echo "<tr>";
        echo "<td width='45px'>";
        echo "<a href='https://forge.sirailgoup.com' target='_blank'>";
        echo "<img class='logo' src='" . PLUGIN_ROOT . "/plugins/gitlabintegration/img/just-logo.png' height='35px' alt='Gitlab Forge' title='Gitlab Forge'>";
        echo "</a>";
        echo "</td>";
        echo "<td>";
        echo "<a class='vsubmit' href='https://forge.sirailgoup.com' target='_blank'>Gitlab Forge</a>";
        echo "</td>";
        echo "</tr>";
        echo "</tbody></table>";
    }

    /**
     * Display contents the summary of profiles Permission.
     *
     * @param int $start
     *
     * @return void
     */
    static function configPage($start)
    {
        $numrows = self::getCountProfilesUsers();

        Html::printPager($start, $numrows, $_SERVER['PHP_SELF'], '');
    }

    /**
     * Display contents the title name of profiles Permission.
     *
     * @param int $nb
     *
     * @return string of the localized name of the type
     */
    static function getTypeName($nb = 0)
    {
        return __('Default Project', 'gitlabintegration');
    }

    /**
     * Display contents the search URL of profiles Permission.
     *
     * @param boolean $full
     *
     * @return string contents the search URL
     */
    static function getSearchURL($full = true)
    {
        global $CFG_GLPI;
        $front_fields = "/plugins/gitlabintegration/front";
        $itemtype = get_called_class();
        $link = "$front_fields/defaultProject.php?itemtype=$itemtype";
        return $link;
    }

    /**
     * Display contents the form URL of defaultProject Permission.
     *
     * @param boolean $full
     *
     * @return string contents the form URL
     */
    static function getFormURL($full = true)
    {
        global $CFG_GLPI;
        $front_fields = "/plugins/gitlabintegration/front";
        $itemtype = get_called_class();
        $link = "$front_fields/defaultProject.form.php?itemtype=$itemtype";
        return $link;
    }

    /**
     * Display contents the form body of profiles Permission.
     *
     * @param void
     *
     * @return void
     */
    public function showForm($ID, array $options = [])
    {
        $projects = PluginGitlabIntegrationCategoriesProjects::projects();
        echo '<div class="glpi_tabs new_form_tabs">';
        echo '   <div id="tabspanel" class="center-h">';
        echo '      <div class="center vertical ui-tabs ui-widget ui-widget-content ui-corner-all ui-tabs-vertical ui-helper-clearfix ui-corner-left">';
        echo '           <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist">';
        echo '              <li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active" role="tab" tabindex="0" aria-controls="ui-tabs-1" aria-labelledby="ui-id-2" aria-selected="true">';
        echo '                 <a title="Block" href="#" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-2">';
        echo self::getTypeName();
        echo '                 </a>';
        echo '              </li>';
        echo '           </ul>';
        echo '           <div id="ui-tabs-1" class="ui-tabs-panel ui-widget-content ui-corner-bottom table-form" aria-live="polite" aria-labelledby="ui-id-2" role="tabpanel" aria-expanded="true" aria-hidden="false">';
        echo '               <div class="form-custom">';
        echo '                   <div class="top-form">' . __('Select a Default Project', 'gitlabintegration') . '</div>';
        echo '                   <div class="flex">';
        echo '                     <div class="top-form left label-form"><label for="default_project">' .  __('Gitlab Project', 'gitlabintegration') . '</label></div>';
        echo '                     <div class="left value-form">';
        echo '                       <select id="default_project" name="default_project" style="padding:5px">';
        foreach ($projects as $project) {
            echo "<option value='$project->id'>";
            echo $project->name_with_namespace;
            echo "</option>";
        }
        echo '                       </select>';
        echo '                     </div>';
        echo '                   </div>';
        echo '                   <div class="button">';
        echo '                       <div class="primary-button" onClick="setDefault()">' . __('Set default Project', 'gitlabintegration') . '</div>';
        echo '                   </div>';
        echo '               </div>';
        echo '           </div>';
        echo '       </div>';
        echo '   </div>';
        echo '</div>';
    }

    /**
     * Display contents the principal form of profiles Permission.
     *
     * @param int $start
     *
     * @return void
     */
    static function massiveActions($start)
    {
        self::tableMassiveActions($start);
    }

    /**
     * Display contains the options of search in permissions profiles.
     *
     * @param void
     *
     * @return array $tab
     */
    function rawSearchOptions()
    {
        $tab = [];

        $tab[] = [
            'id'            => 1,
            'table'         => self::getTable(),
            'field'         => 'profile',
            'name'          => __("Profile", "gitlabintegration"),
            'massiveaction' => false,
        ];

        $tab[] = [
            'id'            => 2,
            'table'         => self::getTable(),
            'field'         => 'user',
            'name'          => __("Created By", "gitlabintegration"),
            'massiveaction' => false,
        ];

        return $tab;
    }

    /**
     * Display contents the principal table of profiles Permission.
     *
     * @param int $start
     *
     * @return void
     */
    private static function tableMassiveActions($start)
    {
        echo '<div class="center">';
        echo '<table border="0" class="tab_cadrehov">';
        self::titleTable(1);
        self::bodyTable($start);
        self::titleTable(2);
        echo '</table>';
        echo '</div>';
    }

    /**
     * Display contents the title of principal Table of profiles Permission.
     *
     * @param void
     *
     * @return void
     */
    private static function titleTable($id)
    {
        echo '<thread>';
        echo '<tbody>';
        echo '<tr class="tab_bg_2">';
        echo '<th class="left" style="width:30%">';
        echo '<a href="#">' . __('Gitlab Project', 'gitlabintegration') . '</a>';
        echo '</th>';
        echo '<th class="left" style="width:35%">';
        echo '<a href="#">' . __('Action', 'gitlabintegration') . '</a>';
        echo '</th>';
        echo '</tr>';
        echo '</tbody>';
        echo '</thread>';
    }

    /**
     * Display contents the body of the principal table of profiles Permission.
     *
     * @param int $start
     *
     * @return void
     */
    private static function bodyTable($start)
    {
        $limit = $_SESSION['glpilist_limit'];

        $result = self::getDefaultProject();

        echo '<tbody id="data">';

        $count = 0;
        $countStart = 0;
        if ($start <= $count) {
            if ($countStart < $limit) {
                foreach ($result as $project) {
                    $projectName = $project['project_name'];
                    $id          = $project['project_id'];
                    echo '<tr class="tab_bg_2">';
                    echo '<td valign="middle" align="center">';
                    echo $projectName;
                    echo '</td>';
                    echo '<td valign="middle" align="center">';
                    echo '<button class="primary-button" onclick="removeDefault(' . $id . ')">' . __('Remove as Default') . '</button>';
                    echo '</td>';
                    echo '</tr>';
                }
            }
            $countStart++;
        }
        $count++;
        echo '</tbody>';
    }

    /**
     * Display contents the profiles of profiles Permission.
     *
     * @param void
     *
     * @return array contents the columns of table glpi_plugin_gitlab_profiles_users
     */
    private static function getDefaultProject()
    {
        global $DB;
        $result = $DB->request("SELECT * FROM glpi_plugin_gitlab_projects WHERE general = 1");
        return $result;
    }

    /**
     * Display return registers amount.
     *
     * @param void
     *
     * @return int $amount
     */
    private static function getCountProfilesUsers()
    {
        global $DB;
        $result = $DB->request(['FROM' => 'glpi_plugin_gitlab_profiles_users', 'COUNT' => 'amount']);

        foreach ($result as $row) {
            $amount = $row['amount'];
        }

        return $amount;
    }

    /**
     * Display contents at the component of permission profiles.
     *
     * @param array $options
     *
     * @return Dropdown component
     */
    static function dropdownActions(array $options = [])
    {
        $p = [
            'name'     => 'actions',
            'value'    => 0,
            'showtype' => 'normal',
            'display'  => true,
        ];

        if (is_array($options) && count($options)) {
            foreach ($options as $key => $val) {
                $p[$key] = $val;
            }
        }

        $values = [];
        $values[0] = '----';
        $values[1] = __('Permanently Delete', 'gitlabintegration');

        return Dropdown::showFromArray($p['name'], $values, $p);
    }
}
