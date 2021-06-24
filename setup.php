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
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['archibp'] = true;
   $PLUGIN_HOOKS['change_profile']['archibp'] = ['PluginArchibpProfile', 'initProfile'];
//   $PLUGIN_HOOKS['assign_to_ticket']['archibp'] = false;
   
   //$PLUGIN_HOOKS['assign_to_ticket_dropdown']['archibp'] = true;
   //$PLUGIN_HOOKS['assign_to_ticket_itemtype']['archibp'] = ['PluginArchibpTask_Item'];
   
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
   //Plugin::registerClass('PluginArchibpTask_Item',
   //                      ['ticket_types' => true]);
      
   if (Session::getLoginUserID()) {

      if (Session::haveRight("plugin_archibp", READ)) {

         $PLUGIN_HOOKS['menu_toadd']['archibp'] = ['assets'   => 'PluginArchibpMenu'];
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
   }
}

// Get the name and the version of the plugin - Needed
function plugin_version_archibp() {

   return array (
      'name' => _n('Business Process', 'Business Processes', 2, 'archibp'),
      'version' => '1.0.1',
      'author'  => "Eric Feron",
      'license' => 'GPLv2+',
      'homepage'=>'https://github.com/ericferon/glpi-archibp',
      'requirements' => [
         'glpi' => [
            'min' => '9.5',
            'dev' => false
         ]
      ]
   );

}

// Optional : check prerequisites before install : may print errors or add to message after redirect
function plugin_archibp_check_prerequisites() {
   global $DB;
   if (version_compare(GLPI_VERSION, '9.5', 'lt')
       || version_compare(GLPI_VERSION, '9.6', 'ge')) {
      if (method_exists('Plugin', 'messageIncompatible')) {
         echo Plugin::messageIncompatible('core', '9.5');
      }
      return false;
   } else {
		$query = "select * from glpi_plugins where directory = 'archisw' and state = 1";
		$result_query = $DB->query($query);
		if($DB->numRows($result_query) == 1) {
			return true;
		} else {
			echo "the plugin 'archisw' must be installed before using 'dataflows'";
		}
	}
}

// Uninstall process for plugin : need to return true if succeeded : may display messages or add to message after redirect
function plugin_archibp_check_config() {
   return true;
}

function plugin_datainjection_migratetypes_archibp($types) {
   $types[2400] = 'PluginArchibpTask';
   return $types;
}

?>
