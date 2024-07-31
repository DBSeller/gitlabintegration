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
 * Summary of PluginGitlabIntegrationCategoriesProjects
 * */

class PluginGitlabIntegrationCategoriesProjects extends CommonDBTM
{
    static $rightname = 'categoriesProjects';

    /**
     * Display contents the create of CategoriesProjects Permission.
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
     * Display contents the title of profiles Permission.
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
     * Display contents the summary of categories and projects.
     *
     * @param int $start
     *
     * @return void
     */
    static function configPage($start)
    {
        $numrows = self::getCountCategoriesProjects();

        Html::printPager($start, $numrows, $_SERVER['PHP_SELF'], '');
    }

    /**
     * Display contents the title name of categories and projects.
     *
     * @param int $nb
     *
     * @return string of the localized name of the type
     */
    static function getTypeName($nb = 0)
    {
        return __('GLPI categories', 'gitlabintegration');
    }

    /**
     * Display contents the search URL of categories and projects.
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
        $link = "$front_fields/categoriesProjects.php?itemtype=$itemtype";
        return $link;
    }

    /**
     * Display contents the form URL of categories and projects.
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
        $link = "$front_fields/categoriesProjects.form.php?itemtype=$itemtype";
        return $link;
    }

    /** 
     *Genrate a combobox for Gitlab Projects 
     */
    public static function projects()
    {
        $varIni = parse_ini_file(GLPI_ROOT."/plugins/gitlabintegration/gitlabintegration.ini");

        $url = $varIni["GITLAB_URL"]."/api/v4/projects";
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

        return $result;
    }

    /** 
     *Generate a combobox for Glpi Categories 
     */
    public static function categories()
    {
        global $DB;

        $categories = $DB->request(['SELECT' => ['id', 'name'], 'FROM' => 'glpi_itilcategories']);

        foreach ($categories as $category) {
            echo "<option value=\"" . $category['id'] . "\">";
            echo $category['name'];
            echo "</option>";
        }
    }

    /**
     * Display contents the form body of profiles Permission.
     *
     * @param void
     *
     * @return void
     */
    public  function showForm($ID, $options= [])
    {
        $projects = self::projects();

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
        echo '                   <div class="top-form">' . __('Associate a Gitlab Project to a Category', 'gitlabintegration') . '</div>';
        echo '                   <div class="flex">';
        echo '                     <div class="top-form left label-form"><label for="gitlabProject">' .  __('Gitlab Project', 'gitlabintegration') . '</label></div>';
        echo '                     <div class="left value-form">';
        echo '                     <select id="gitlabProject" style="padding: 5px">';
        foreach ($projects as $project) {
            echo "<option value=\"" . $project->id . "\">";
            echo $project->name_with_namespace;
            echo '</option>';
        }
        echo '                     </select>';
        echo '                     </div>';
        echo '                     <div class="top-form left label-form"><label for="glpiCategory">' .  __('Category', 'gitlabintegration') . '</label></div>';
        echo '                     <div class="left value-form">';
        echo '                     <select id="glpiCategory" style="padding: 5px">';
        self::categories();
        echo '                     </select>';
        echo '                     </div>';
        echo '                   </div>';
        echo '                   <div class="button">';
        echo '                       <div class="primary-button" onClick="associate()">' . __('Associate', 'gitlabintegration') . '</div>';
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

    static function dialogActions()
    {
        echo '<div id="favDialog" role="dialog" title="' . __('Actions', 'gitlabintegration') . '" style="width: 40% !important; height: 30% !important">';
        echo '   <div>';
        echo '      <div id="no_information" class="body-dialog">';
        echo '         <img src="/pics/warning.png" alt="Warning"><br><br>';
        echo '         <span class="b">' . __('No selected items', 'gitlabintegration') . '</span><br>';
        echo '      </div>';
        echo '      <div id="options_to_select" class="body-dialog">';
        echo '         <div class="inline" style="margin-right:10px">' . __('Actions', 'gitlabintegration') . ': </div>';
        $dropdown = self::dropdownActions(['value' => 'actions']);
        echo '      </div>';
        echo '      <div id="button_confirm_action" style="margin:15px" class="body-dialog">';
        echo '         <div class="primary-button" onClick="removeAssociation()">Post</div>';
        echo '      </div>';
        echo '   </div>';
        echo '</div>';
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
        echo '<tbody id="principal_' . $id . '">';
        echo '<tr class="tab_bg_2">';
        echo '<th class="left" style="width:30%">';
        echo '<a href="#">' . __('Gitlab Project', 'gitlabintegration') . '</a>';
        echo '</th>';
        echo '<th class="left" style="width:35%">';
        echo '<a href="#">' . __('GLPI Category', 'gitlabintegration') . '</a>';
        echo '</th>';
        echo '<th>';
        echo '<a href="#">' . __('Associated at', 'gitlabintegration') . '</a>';
        echo '</th>';
        echo '</th>';
        echo '<th>';
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

        $result = self::getCategoriesProjetcs();


        echo '<tbody id="data">';

        $count = 0;
        $countStart = 0;
        foreach ($result as $row) {
            if ($start <= $count) {
                if ($countStart < $limit) {
                    $gitlabProject = $row['project_name'];
                    $glpiCategory  = $row['name'];
                    $created       = $row['created_at'];
                    $project_id    = $row['project_id'];
                    $category_id   = $row['id'];

                    echo '<tr class="tab_bg_2">';
                    echo '<td valign="middle" align="center">';
                    echo $gitlabProject;
                    echo '</td>';
                    echo '<td valign="middle" align="center">';
                    echo $glpiCategory;
                    echo '</td>';
                    echo '<td valign="middle" align="center">';
                    echo $created;
                    echo '</td>';
                    echo '<td valign="middle" align="center">';
                    echo '<button class="primary-button" onclick="removeAssociation(' . $project_id . ', ' . $category_id . ')">Delete</button>';
                    echo '</td>';
                    echo '</tr>';
                }
                $countStart++;
            }
            $count++;
        }
        echo '</tbody>';
    }

    /**
     * Display contents the profiles of profiles Permission.
     *
     * @param void
     *
     * @return array contents the columns of table glpi_plugin_gitlab_profiles_users
     */
    private static function getCategoriesProjetcs()
    {
        global $DB;
        $result = $DB->request(
            [
                'FROM' =>
                'glpi_plugin_gitlab_projects',
                'INNER JOIN' => [
                    'glpi_itilcategories' => [
                        'FKEY' => [
                            'glpi_plugin_gitlab_projects' => 'category_id',
                            'glpi_itilcategories' => 'id'
                        ]
                    ]
                ],

            ]
        );
        return $result;
    }

    /**
     * Display return registers amount.
     *
     * @param void
     *
     * @return int $amount
     */
    private static function getCountCategoriesProjects()
    {
        global $DB;
        $result = $DB->request(['FROM' => 'glpi_plugin_gitlab_projects', 'COUNT' => 'amount']);

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
