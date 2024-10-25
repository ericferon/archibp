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

// Init the hooks of the plugins -Needed
function plugin_init_archibp() {
   global $PLUGIN_HOOKS, $CFG_GLPI;

   $PLUGIN_HOOKS['csrf_compliant']['archibp'] = true;
   $PLUGIN_HOOKS['change_profile']['archibp'] = ['PluginArchibpProfile', 'initProfile'];
//   $PLUGIN_HOOKS['assign_to_ticket']['archibp'] = false;
   
   //$PLUGIN_HOOKS['assign_to_ticket_dropdown']['archibp'] = true;
   //$PLUGIN_HOOKS['assign_to_ticket_itemtype']['archibp'] = ['PluginArchibpTask_Item'];
   
   $CFG_GLPI['impact_asset_types']['PluginArchibpTask'] = Plugin::getWebDir("archibp", false)."/bp.png";

   Plugin::registerClass('PluginArchibpTask', array(
         'linkgroup_tech_types'   => true,
         'linkuser_tech_types'    => true,
         'document_types'         => true,
//         'ticket_types'           => true,
         'helpdesk_visible_types' => true//,
//         'addtabon'               => 'Supplier'
   ));
   Plugin::registerClass('PluginArchibpProfile',
                         ['addtabon' => 'Profile']);
                         
   if (class_exists('PluginArchiswSwcomponent')) {
      PluginArchiswSwcomponent::registerType('PluginArchibpTask');
   }
   if (class_exists('PluginArchimapGraph')) {
      PluginArchimapGraph::registerType('PluginArchibpTask');
   }
   if (class_exists('PluginArchidataDataelement')) {
      PluginArchidataDataelement::registerType('PluginArchibpTask');
   }
      
   if (Session::getLoginUserID()) {

      // link to fields plugin
      $plugin = new Plugin();
      if ($plugin->isActivated('fields')
      && Session::haveRight("plugin_archibp", READ)) 
      {
         $PLUGIN_HOOKS['plugin_fields']['archibp'] = 'PluginArchibpTask';
      }

      if (Session::haveRight("plugin_archibp", READ)) {

         $PLUGIN_HOOKS['menu_toadd']['archibp']['assets'] = 'PluginArchibpMenu';
      }

      if (Session::haveRight("plugin_archibp_configuration", READ)) {

         $PLUGIN_HOOKS['menu_toadd']['archibp']['config'] = 'PluginArchibpConfigbpMenu';
      }

      if (Session::haveRight("plugin_archibp", READ)
          || Session::haveRight("config", UPDATE)) {
         $PLUGIN_HOOKS['config_page']['archibp']        = 'front/configbp.php';
      }

      if (Session::haveRight("plugin_archibp", UPDATE)) {
         $PLUGIN_HOOKS['use_massive_action']['archibp']=1;
      }

      if (class_exists('PluginArchibpTask_Item')) { // only if plugin activated
         $PLUGIN_HOOKS['plugin_datainjection_populate']['archibp'] = 'plugin_datainjection_populate_archibp';
      }

      // End init, when all types are registered
      $PLUGIN_HOOKS['post_init']['archibp'] = 'plugin_archibp_postinit';

      // Import from Data_Injection plugin
      $PLUGIN_HOOKS['migratetypes']['archibp'] = 'plugin_datainjection_migratetypes_archibp';
      $PLUGIN_HOOKS['pre_item_update']['archibp'] = ['PluginArchibpConfigbp' => 'hook_pre_item_update_archibp_configbp', 
                                                   'PluginArchibpConfigbpLink' => 'hook_pre_item_update_archibp_configbplink'];
      $PLUGIN_HOOKS['pre_item_add']['archibp'] = ['PluginArchibpConfigbp' => 'hook_pre_item_add_archibp_configbp', 
                                                   'PluginArchibpConfigbpLink' => 'hook_pre_item_add_archibp_configbplink'];
      $PLUGIN_HOOKS['pre_item_purge']['archibp'] = ['PluginArchibpConfigbp' => 'hook_pre_item_purge_archibp_configbp', 
                                                   'PluginArchibpConfigbpLink' => 'hook_pre_item_purge_archibp_configbplink'];

   }
}

// Get the name and the version of the plugin - Needed
function plugin_version_archibp() {

   return array (
      'name' => _n('Business Process', 'Business Processes', 2, 'archibp'),
      'version' => '2.0.11',
      'author'  => "Eric Feron",
      'license' => 'GPLv2+',
      'homepage'=>'https://github.com/ericferon/glpi-archibp',
      'requirements' => [
         'glpi' => [
            'min' => '10.0',
            'dev' => false
         ]
      ]
   );

}

// Optional : check prerequisites before install : may print errors or add to message after redirect
function plugin_archibp_check_prerequisites() {
   global $DB;
   if (version_compare(GLPI_VERSION, '10.0', 'lt')
       || version_compare(GLPI_VERSION, '10.1', 'ge')) {
      if (method_exists('Plugin', 'messageIncompatible')) {
         echo Plugin::messageIncompatible('core', '10.0');
      }
      return false;
   } else {
		$query = "select * from glpi_plugins where directory in ('archisw', 'statecheck') and state = 1";
		$result_query = $DB->query($query);
		if($DB->numRows($result_query) == 2) {
			return true;
		} else {
			echo "The 2 plugins 'archisw' (a.k.a Apps structure inventory) and 'statecheck' must be installed before using 'archibp' (Business Process)";
		}
	}
}

// Uninstall process for plugin : need to return true if succeeded : may display messages or add to message after redirect
function plugin_archibp_check_configbp() {
   return true;
}

function plugin_datainjection_migratetypes_archibp($types) {
   $types[2400] = 'PluginArchibpTask';
   return $types;
}

// Uninstall process for plugin : need to return true if succeeded : may display messages or add to message after redirect
function hook_pre_item_add_archibp_configbp(CommonDBTM $item) {
   global $DB;
   $fieldname = $item->fields['name'];
   $dbfield = new PluginArchibpConfigbpDbfieldtype;
   if ($dbfield->getFromDB($item->fields['plugin_archibp_configbpdbfieldtypes_id'])) {
      $fieldtype = $dbfield->fields['name'];
      $query = "ALTER TABLE `glpi_plugin_archibp_tasks` ADD COLUMN IF NOT EXISTS $fieldname $fieldtype";
      if($item->fields['plugin_archibp_configbpdatatypes_id'] == 6) {// if dropdown, add key
         $query .= ", ADD KEY IF NOT EXISTS $fieldname ($fieldname)";
      }
      $result = $DB->query($query);
      return true;
   }
   return false;
}
function hook_pre_item_update_archibp_configbp(CommonDBTM $item) {
   global $DB;
   $oldfieldname = $item->fields['name'];
   $newfieldname = $item->input['name'];
   $dbfield = new PluginArchibpConfigbpDbfieldtype;
   if ($dbfield->getFromDB($item->fields['plugin_archibp_configbpdbfieldtypes_id'])) {
      $fieldtype = $dbfield->fields['name'];
      if ($oldfieldname != $newfieldname) {
         $query = "ALTER TABLE `glpi_plugin_archibp_tasks` CHANGE COLUMN $oldfieldname $newfieldname $fieldtype";
      } else {
         $query = "ALTER TABLE `glpi_plugin_archibp_tasks` MODIFY $newfieldname $fieldtype";
      }
      if($item->input['plugin_archibp_configbpdatatypes_id'] == 6) {// if dropdown, add key
         $query .= ", ADD KEY IF NOT EXISTS $newfieldname ($newfieldname)";
      }
      $result = $DB->query($query);
      return true;
   }
   return false;
}
function hook_pre_item_purge_archibp_configbp(CommonDBTM $item) {
   global $DB;
   $fieldname = $item->fields['name'];
   $query = "ALTER TABLE `glpi_plugin_archibp_tasks` DROP COLUMN IF EXISTS $fieldname";
   $result = $DB->query($query);
   return true;
}
?>
