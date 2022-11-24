<?php
/*
 -------------------------------------------------------------------------
 Archibp plugin for GLPI
 Copyright (C) 2009-2021 by Eric Feron.
 -------------------------------------------------------------------------

 LICENSE
      
 This file is part of Archibp.

 Archibp is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 at your option any later version.

 Archibp is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Archibp. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */

function plugin_archibp_install() {
   global $DB;

   include_once (Plugin::getPhpDir("archibp")."/inc/profile.class.php");

   if (!$DB->TableExists("glpi_plugin_archibp_tasks")) {

		$DB->runFile(Plugin::getPhpDir("archibp")."/sql/empty-1.0.2.sql");
	}
   else if (!$DB->TableExists("glpi_plugin_archibp_tasktargets")) {

		$DB->runFile(Plugin::getPhpDir("archibp")."/sql/update-1.0.0.sql");
	}

   
   PluginArchibpProfile::initProfile();
   PluginArchibpProfile::createFirstAccess($_SESSION['glpiactiveprofile']['id']);
//   $migration = new Migration("2.0.0");
//   $migration->dropTable('glpi_plugin_archibp_profiles');
   
   return true;
}

function plugin_archibp_uninstall() {
   global $DB;
   
   include_once (Plugin::getPhpDir("archibp")."/inc/profile.class.php");
   include_once (Plugin::getPhpDir("archibp")."/inc/menu.class.php");
   
	$tables = ["glpi_plugin_archibp_tasks",
					"glpi_plugin_archibp_criticities",
					"glpi_plugin_archibp_tasktypes",
					"glpi_plugin_archibp_tasks_items",
					"glpi_plugin_archibp_profiles",
                    "glpi_plugin_archibp_tasktargets"
              ];

   foreach($tables as $table)
      $DB->query("DROP TABLE IF EXISTS `$table`;");

	$views = ["glpi_plugin_archibp_swcomponents"];
				
	foreach($views as $view)
		$DB->query("DROP VIEW IF EXISTS `$view`;");

	$tables_glpi = ["glpi_displaypreferences",
               "glpi_documents_items",
               "glpi_savedsearches",
               "glpi_logs",
               "glpi_items_tickets",
               "glpi_notepads",
               "glpi_dropdowntranslations",
               "glpi_impactitems"];

   foreach($tables_glpi as $table_glpi)
      $DB->query("DELETE FROM `$table_glpi` WHERE `itemtype` LIKE 'PluginArchibp%' ;");

   $DB->query("DELETE
                  FROM `glpi_impactrelations`
                  WHERE `itemtype_source` IN ('PluginArchibpTask')
                    OR `itemtype_impacted` IN ('PluginArchibpTask')");

   if (class_exists('PluginDatainjectionModel')) {
      PluginDatainjectionModel::clean(['itemtype'=>'PluginArchibpTask']);
   }
   
   //Delete rights associated with the plugin
   $profileRight = new ProfileRight();
   foreach (PluginArchibpProfile::getAllRights() as $right) {
      $profileRight->deleteByCriteria(['name' => $right['field']]);
   }
   PluginArchibpMenu::removeRightsFromSession();
   PluginArchibpProfile::removeRightsFromSession();
   
   return true;
}

function plugin_archibp_postinit() {
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['item_purge']['archibp'] = [];

   foreach (PluginArchibpTask::getTypes(true) as $type) {

      $PLUGIN_HOOKS['item_purge']['archibp'][$type]
         = ['PluginArchibpTask_Item','cleanForItem'];

      CommonGLPI::registerStandardTab($type, 'PluginArchibpTask_Item');
   }
}


// Define dropdown relations
function plugin_archibp_getTaskRelations() {

   $plugin = new Plugin();
   if ($plugin->isActivated("archibp"))
		return ["glpi_plugin_archibp_tasks"=>["glpi_plugin_archibp_tasks_items"=>"plugin_archibp_tasks_id"],
					 "glpi_plugin_archibp_tasktypes"=>["glpi_plugin_archibp_tasks"=>"plugin_archibp_tasktypes_id"],
					 "glpi_plugin_archibp_criticities"=>["glpi_plugin_archibp_tasks"=>"plugin_archibp_criticities_id"],
					 "glpi_plugin_archibp_swcomponents"=>["glpi_plugin_archibp_tasks"=>"plugin_archibp_swcomponents_id"],
					 "glpi_plugin_archibp_tasktargets"=>["glpi_plugin_archibp_tasks"=>"plugin_archibp_tasktargets_id"],
					 "glpi_entities"=>["glpi_plugin_archibp_tasks"=>"entities_id"],
					 "glpi_groups"=>["glpi_plugin_archibp_tasks"=>"groups_id"],
					 "glpi_users"=>["glpi_plugin_archibp_tasks"=>"users_id"]
					 ];
   else
      return [];
}

// Define Dropdown tables to be manage in GLPI :
function plugin_archibp_getDropdown() {

   $plugin = new Plugin();
   if ($plugin->isActivated("archibp"))
		return array('PluginArchibpTasktype'=>PluginArchibpTasktype::getTypeName(2), //getTypeName(2) does not work
                'PluginArchibpTaskTarget'=>PluginArchibpTaskTarget::getTypeName(2),
                'PluginArchibpCriticity'=>PluginArchibpCriticity::getTypeName(2)
                );
   else
      return [];
}

////// SEARCH FUNCTIONS ///////() {

function plugin_archibp_getAddSearchOptions($itemtype) {

   $sopt=[];

   if (in_array($itemtype, PluginArchibpTask::getTypes(true))) {
      if (Session::haveRight("plugin_archibp", READ)) {

         $sopt[2410]['table']         ='glpi_plugin_archibp_tasks';
         $sopt[2410]['field']         ='name';
         $sopt[2410]['name']          = PluginArchibpTask::getTypeName(2)." - ".__('Name');
         $sopt[2410]['forcegroupby']  = true;
         $sopt[2410]['datatype']      = 'itemlink';
         $sopt[2410]['massiveaction'] = false;
         $sopt[2410]['itemlink_type'] = 'PluginArchibpTask';
         $sopt[2410]['joinparams']    = ['beforejoin'
                                                => ['table'      => 'glpi_plugin_archibp_tasks_items',
                                                         'joinparams' => ['jointype' => 'itemtype_item']]];

     }
   }
   return $sopt;
}

function plugin_archibp_giveItem($type,$ID,$data,$num) {
   global $DB;

   return "";
}

////// SPECIFIC MODIF MASSIVE FUNCTIONS ///////

function plugin_archibp_MassiveActions($type) {

    $plugin = new Plugin();
    if ($plugin->isActivated('archibp')) {
        if (in_array($type,PluginArchibpTask::getTypes(true))) {
            return ['PluginArchibpTask'.MassiveAction::CLASS_ACTION_SEPARATOR.'plugin_archibp__add_item' =>
                                                              __('Associate to the Business Process', 'archibp')];
        }
    }
    return [];
}

/*
function plugin_archibp_MassiveActionsDisplay($options=[]) {

   $task=new PluginArchibpTask;

   if (in_array($options['itemtype'], PluginArchibpTask::getTypes(true))) {

      $task->dropdownTasks("plugin_archibp_task_id");
      echo "<input type=\"submit\" name=\"massiveaction\" class=\"submit\" value=\""._sx('button', 'Post')."\" >";
   }
   return "";
}

function plugin_archibp_MassiveActionsProcess($data) {

   $res = ['ok' => 0,
            'ko' => 0,
            'noright' => 0];

   $task_item = new PluginArchibpTask_Item();

   switch ($data['action']) {

      case "plugin_archibp_add_item":
         foreach ($data["item"] as $key => $val) {
            if ($val == 1) {
               $input = ['plugin_archibp_task_id' => $data['plugin_archibp_task_id'],
                              'items_id'      => $key,
                              'itemtype'      => $data['itemtype']];
               if ($task_item->can(-1,'w',$input)) {
                  if ($task_item->can(-1,'w',$input)) {
                     $task_item->add($input);
                     $res['ok']++;
                  } else {
                     $res['ko']++;
                  }
               } else {
                  $res['noright']++;
               }
            }
         }
         break;
   }
   return $res;
}
*/
function plugin_datainjection_populate_archibp() {
   global $INJECTABLE_TYPES;
   $INJECTABLE_TYPES['PluginArchibpTaskInjection'] = 'datainjection';
}



?>
