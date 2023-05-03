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

		$DB->runFile(Plugin::getPhpDir("archibp")."/sql/empty-2.0.1.sql");
	}
   else 
   {
      if (!$DB->TableExists("glpi_plugin_archibp_tasktargets")) {

		$DB->runFile(Plugin::getPhpDir("archibp")."/sql/update-1.0.0.sql");
      }
      if (!$DB->TableExists("glpi_plugin_archibp_taskstates")) {

		$DB->runFile(Plugin::getPhpDir("archibp")."/sql/update-1.0.1.sql");
      }
      if (!$DB->TableExists("glpi_plugin_archibp_configbps")) {
         $DB->runFile(Plugin::getPhpDir("archibp")."/sql/update-2.0.0.sql");
      }

      if ($DB->numrows($DB->query("SELECT * from glpi_plugin_archibp_configbphaligns where id = '7'")) == 0) {
         $DB->runFile(Plugin::getPhpDir("archisw")."/sql/update-2.0.1.sql");
      }
   }
   // regenerate configbpured fields
   if ($DB->TableExists("glpi_plugin_archibp_configbplinks") && $DB->TableExists("glpi_plugin_archibp_configbps")) {
      $query = "SELECT `glpi_plugin_archibp_configbplinks`.`name` as `classname`, `is_entity_limited`, `is_tree_dropdown`
               FROM `glpi_plugin_archibp_configbplinks` 
               JOIN `glpi_plugin_archibp_configbps`  ON `glpi_plugin_archibp_configbplinks`.`id` = `glpi_plugin_archibp_configbps`.`plugin_archibp_configbplinks_id` 
               WHERE `glpi_plugin_archibp_configbplinks`.`name` like 'PluginArchibp%'";
      $result = $DB->query($query);
      $item = new CommonDBTM;
      while ($data = $DB->fetchAssoc($result)) {
         $item->input['name'] = $data['classname'];
         $item->input['is_entity_limited'] = $data['is_entity_limited'];
         $item->input['is_tree_dropdown'] = $data['is_tree_dropdown'];
         $item->input['as_view_on'] = $data['as_view_on'];
         $item->input['viewlimit'] = $data['viewlimit'];
         hook_pre_item_add_archibp_configbplink($item); // simulate the creation of this field
      }
      // refresh with new files
      header("Refresh:0");
   }
   
   PluginArchibpProfile::initProfile();
   PluginArchibpProfile::createFirstAccess($_SESSION['glpiactiveprofile']['id']);
   
   return true;
}

function plugin_archibp_uninstall() {
   global $DB;
   
   include_once (Plugin::getPhpDir("archibp")."/inc/profile.class.php");
   include_once (Plugin::getPhpDir("archibp")."/inc/menu.class.php");
   
   $query = "SELECT `id` FROM `glpi_plugin_statecheck_tables` WHERE `name` = 'glpi_plugin_archibp_configbps'";
   $result = $DB->query($query);
   $rowcount = $DB->numrows($result);
   if ($rowcount > 0) {
      while ($data = $DB->fetchAssoc($result)) {
         $tableid = $data['id'];
         $rulequery = "SELECT `id` FROM `glpi_plugin_statecheck_rules` WHERE `plugin_statecheck_tables_id` = '".$tableid."'";
         $ruleresult = $DB->query($rulequery);
         while ($ruledata = $DB->fetchAssoc($ruleresult)) {
            $ruleid = $ruledata['id'];
            $query = "DELETE FROM `glpi_plugin_statecheck_ruleactions` WHERE `plugin_statecheck_rules_id` = '".$ruleid."'";
            $DB->query($query);
            $query = "DELETE FROM `glpi_plugin_statecheck_rulecriterias` WHERE `plugin_statecheck_rules_id` = '".$ruleid."'";
            $DB->query($query);
         }
         $query = "DELETE FROM `glpi_plugin_statecheck_rules` WHERE `plugin_statecheck_tables_id` = '".$tableid."'";
         $DB->query($query);
      }
      $query = "DELETE FROM `glpi_plugin_statecheck_tables` WHERE `name` like 'glpi_plugin_archibp_%'";
      $result = $DB->query($query);
   }

	$tables = ["glpi_plugin_archibp_tasks",
					"glpi_plugin_archibp_tasks_items",
                    "glpi_plugin_archibp_configbps",
                    "glpi_plugin_archibp_configbpfieldgroups",
                    "glpi_plugin_archibp_configbphaligns",
                    "glpi_plugin_archibp_configbpdbfieldtypes",
                    "glpi_plugin_archibp_configbpdatatypes",
                    "glpi_plugin_archibp_configbplinks",
					"glpi_plugin_archibp_profiles"
              ];

   $query = "SELECT `name` FROM `glpi_plugin_archibp_configbplinks` WHERE `name` LIKE 'PluginArchibp%' AND (`as_view_on` IS NULL OR `as_view_on` = '')";
   $result = $DB->query($query);
   while ($data = $DB->fetchAssoc($result)) {
      $tablename = CommonDBTM::getTable($data['name']);
      if (!in_array($tablename,$tables))
         $tables[] = $tablename;
   }

   foreach($tables as $table)
      $DB->query("DROP TABLE IF EXISTS `$table`;");

   $views = ["glpi_plugin_archibp_swcomponents"];
   $query = "SELECT `name` FROM `glpi_plugin_archibp_configbplinks` WHERE `name` LIKE 'PluginArchibp%' AND (`as_view_on` IS NOT NULL AND `as_view_on` <> '')";
   $result = $DB->query($query);
   while ($data = $DB->fetchAssoc($result)) {
      $tablename = CommonDBTM::getTable($data['name']);
      if (!in_array($tablename,$tables))
         $views[] = $tablename;
   }
				
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
   if ($plugin->isActivated("archibp")) {
      $tables = ["glpi_plugin_archibp_tasks"=>["glpi_plugin_archibp_tasks_items"=>"plugin_archibp_tasks_id"],
					 "glpi_entities"=>["glpi_plugin_archibp_tasks"=>"entities_id"],
					 "glpi_groups"=>["glpi_plugin_archibp_tasks"=>"groups_id"],
					 "glpi_users"=>["glpi_plugin_archibp_tasks"=>"users_id"]
					 ];

      $query = "SELECT `name` FROM `glpi_plugin_archibp_configbplinks` WHERE `name` like 'PluginArchibp%'";
      $result = $DB->query($query);
      while ($data = $DB->fetchAssoc($result)) {
         $tablename = CommonDBTM::getTable($data['name']);
         if (!in_array($tablename,$tables)) {
            $fieldname = substr($tablename, 5)."_id";
            $tables[$tablename] = ["glpi_plugin_archibp_tasks"=>$fieldname];
         }
      }
      return $tables;
   }
   else
      return [];
}

// Define Dropdown tables to be manage in GLPI :
function plugin_archibp_getDropdown() {

   global $DB;

   $plugin = new Plugin();
   if ($plugin->isActivated("archibp")) {
      $classes = [//'PluginArchibpSwcomponentType'=>PluginArchibpSwcomponentType::getTypeName(2),
					 'PluginArchibpConfigbp'=>PluginArchibpConfigbp::getTypeName(2),
					 'PluginArchibpConfigbpFieldgroup'=>PluginArchibpConfigbpFieldgroup::getTypeName(2),
					 'PluginArchibpConfigbpHalign'=>PluginArchibpConfigbpHalign::getTypeName(2),
					 'PluginArchibpConfigbpDbfieldtype'=>PluginArchibpConfigbpDbfieldtype::getTypeName(2),
					 'PluginArchibpConfigbpDatatype'=>PluginArchibpConfigbpDatatype::getTypeName(2),
					 'PluginArchibpConfigbpLink'=>PluginArchibpConfigbpLink::getTypeName(2)
		];

      $query = "SELECT `glpi_plugin_archibp_configbplinks`.`name` as `classname`, `glpi_plugin_archibp_configbps`.`description` as `typename` 
               FROM `glpi_plugin_archibp_configbplinks` 
               JOIN `glpi_plugin_archibp_configbps`  ON `glpi_plugin_archibp_configbplinks`.`id` = `glpi_plugin_archibp_configbps`.`plugin_archibp_configbplinks_id` 
               WHERE `glpi_plugin_archibp_configbplinks`.`name` like 'PluginArchibp%' AND (`glpi_plugin_archibp_configbplinks`.`as_view_on` IS NULL OR `glpi_plugin_archibp_configbplinks`.`as_view_on` = '')";
      $result = $DB->query($query);
      while ($data = $DB->fetchAssoc($result)) {
         $classname = $data['classname'];
         if (!in_array($classname,$classes))
            $classes[$classname] = $data['typename'];
      }
      return $classes;
   }
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

function hook_pre_item_add_archibp_configbplink(CommonDBTM $item) {
   global $DB;
   $dir = Plugin::getPhpDir("archibp", true);
   $newclassname = $item->input['name'];
   $newistreedropdown = $item->input['is_tree_dropdown'];
   $newisentitylimited = $item->input['is_entity_limited'];
   $newasviewon = $item->input['as_view_on'];
   $newviewlimit = str_replace("\'", "'", $item->input['viewlimit']); // unescape single quotes
   if (substr($newclassname, 0, 13) == 'PluginArchibp') {
      $rootname = strtolower(substr($newclassname, 13));
      $tablename = 'glpi_plugin_archibp_'.getPlural($rootname);
      $fieldname = 'plugin_archibp_'.getPlural($rootname).'_id';
      if (!empty($newasviewon)) {
         $entities = ($newisentitylimited?" `entities_id`,":"");
         $name = ($newistreedropdown?" `completename`,":" `name`,");
         if (!$newistreedropdown) {
            // new simple dropdown view
            $query = "CREATE VIEW `$tablename` (`id`,$entities `name`, `comment`) AS 
                  SELECT `id`,$entities `name`, `comment` FROM $newasviewon".(empty($newviewlimit)?"":" WHERE $newviewlimit");
         } 
         else { // new treedropdon view
            $query = "CREATE VIEW `$tablename` (`id`,$entities `name`, `comment`, `completename`, `level`, `is_recursive`) AS 
                  SELECT `id`,$entities `name`, `comment`, `completename`, `level`, `is_recursive` FROM $newasviewon".(empty($newviewlimit)?"":" WHERE $newviewlimit");
         }
         $result = $DB->query($query);
      }
      else {
         $entities = ($newisentitylimited?"`entities_id` INT(11) UNSIGNED NOT NULL DEFAULT 0,":"");
         if (!$newistreedropdown) { //dropdown->create table
            $query = "CREATE TABLE IF NOT EXISTS `".$tablename."` (
                  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,".
                  $entities.
                  "`name` VARCHAR(45) NOT NULL,
                  `comment` VARCHAR(255) NULL,
                  `completename` MEDIUMTEXT NULL,
                  PRIMARY KEY (`id`) ,
                  UNIQUE INDEX `".$tablename."_name` (`name`) )
                  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
            $result = $DB->query($query);
         }
         else { //treedropdown->create table
            $query = "CREATE TABLE IF NOT EXISTS `".$tablename."` (
                        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,".
                        $entities.
                        "`is_recursive` BIT NOT NULL DEFAULT 0,
                        `name` VARCHAR(45) NOT NULL,
                        $fieldname INT(11) UNSIGNED NOT NULL DEFAULT 0,
                        `completename` MEDIUMTEXT NULL,
                        `comment` VARCHAR(255) NULL,
                        `level` INT NOT NULL DEFAULT 0,
                        `sons_cache` LONGTEXT NULL,
                        `ancestors_cache` LONGTEXT NULL,
                        PRIMARY KEY (`id`) ,
                        UNIQUE INDEX `".$tablename."_name` (`name`) )
                        DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
            $result = $DB->query($query);
         }
      }
      create_plugin_archibp_classfiles($dir, $newclassname, $newistreedropdown);
   }
}
function hook_pre_item_update_archibp_configbplink(CommonDBTM $item) {
   global $DB;
   $dir = Plugin::getPhpDir("archibp", true);
   $newclassname = $item->input['name'];
   $newistreedropdown = $item->input['is_tree_dropdown'];
   $newasviewon = $item->input['as_view_on'];
   $newviewlimit = str_replace("\'", "'", $item->input['viewlimit']); // unescape single quotes
   $oldclassname = $item->fields['name'];
   $oldistreedropdown = $item->fields['is_tree_dropdown'];
   $oldasviewon = $item->fields['as_view_on'];
   if (substr($newclassname, 0, 13) == 'PluginArchibp') {
      // class is owned by this plugin
      $newrootname = strtolower(substr($newclassname, 13));
      $newfilename = $newrootname;
      $newtablename = 'glpi_plugin_archibp_'.getPlural($newrootname);
      $newfieldname = 'plugin_archibp_'.getPlural($newrootname).'_id';
      if (substr($oldclassname, 0, 13) == 'PluginArchibp') {
         //old and new types are owned by this plugin
         if ($oldclassname != $newclassname) { 
            //dropdown name modified->rename table or view
            $oldrootname = strtolower(substr($oldclassname, 13));
            $oldfilename = $oldrootname;
            $oldtablename = 'glpi_plugin_archibp_'.getPlural($oldrootname);
            $oldfieldname = 'plugin_archibp_'.getPlural($oldrootname).'_id';
            $query = "RENAME TABLE `".$oldtablename."` TO `".$newtablename."`";
            $result = $DB->query($query);
            $query = "UPDATE `glpi_plugin_archibp_configbplinks` SET `name` = '".$newclassname."' WHERE `name` = '".$oldclassname."'";
            $result = $DB->query($query);
         }
         else {// no change dropdown name
            // if dropdown table is a view, replace the old view
            if (!empty($newasviewon)) {
               $entities = ($newisentitylimited?" `entities_id`,":"");
               $name = ($newistreedropdown?" `completename`,":" `name`,");
               if (!$newistreedropdown) {
                  // new simple dropdown view
                  $query = "CREATE OR REPLACE VIEW `$newtablename` (`id`,$entities `name`, `comment`) AS 
                        SELECT `id`,$entities `name`, `comment` FROM $newasviewon".(empty($newviewlimit)?"":" WHERE $newviewlimit");
               } 
               else { // new treedropdon view
                  $query = "CREATE OR REPLACE VIEW `$newtablename` (`id`,$entities `name`, `comment`, `completename`, `level`, `is_recursive`) AS 
                        SELECT `id`,$entities `name`, `comment`, `completename`, `level`, `is_recursive` FROM $newasviewon".(empty($newviewlimit)?"":" WHERE $newviewlimit");
               }
               $result = $DB->query($query);
            }
            else {
               // if dropdown table is really a table ...
               if (!$oldistreedropdown && $newistreedropdown) {
                  // 'is_tree_dropdown' has changed
                  // old type was dropdown and new one is treedropdown=>add the needed fields
                  $query = "ALTER TABLE $newtablename
                     ADD COLUMN `is_recursive` BIT NOT NULL DEFAULT 0 AFTER `id`,
                     ADD COLUMN $newfieldname INT(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `name`,
                     ADD COLUMN `level` INT NOT NULL DEFAULT 0 AFTER `completename`,
                     ADD COLUMN `sons_cache` LONGTEXT NULL AFTER `level`,
                     ADD COLUMN `ancestors_cache` LONGTEXT NULL AFTER `sons_cache`";
                  $result = $DB->query($query);
               }
               else if ($oldistreedropdown && !$newistreedropdown) {
                  // old type was treedropdown and new one is dropdown=>drop the unneeded fields
                  $query = "ALTER TABLE $newtablename
                     DROP COLUMN `is_recursive`,
                     DROP COLUMN $newfieldname,
                     DROP COLUMN `level`,
                     DROP COLUMN `sons_cache`,
                     DROP COLUMN `ancestors_cache`";
                  $result = $DB->query($query);
               }
               // 'is_entity_limited' has changed
               if (!$item->fields['is_entity_limited'] && $item->input['is_entity_limited']) { // 'is_entity_limited' changed from no to yes
               // => add 'entities_id' column to dropdown table
                  $query = "ALTER TABLE $newtablename ADD COLUMN IF NOT EXISTS `entities_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `id`";
                  $result = $DB->query($query);
               }
               else if ($item->fields['is_entity_limited'] && !$item->input['is_entity_limited']) { // 'is_entity_limited' changed from yes to no
               // => drop 'entities_id' column from dropdown table
                  $query = "ALTER TABLE $newtablename DROP COLUMN `entities_id`";
                  $result = $DB->query($query);
               }
            }
         }
      }
      else {// old type wasn't owned by this plugin, but the new one is well owned
         //dropdown new->create table or view
         if (!empty($newasviewon)) {
            $entities = ($newisentitylimited?" `entities_id`,":"");
            $name = ($newistreedropdown?" `completename`,":" `name`,");
            if (!$newistreedropdown) {
               // new simple dropdown view
               $query = "CREATE VIEW `$tablename` (`id`,$entities `name`, `comment`) AS 
                  SELECT `id`,$entities `name`, `comment` FROM $newasviewon".(empty($newviewlimit)?"":" WHERE $newviewlimit");
            } 
            else { // new treedropdon view
               $query = "CREATE VIEW `$tablename` (`id`,$entities `name`, `comment`, `completename`, `level`, `is_recursive`) AS 
                  SELECT `id`,$entities `name`, `comment`, `completename`, `level`, `is_recursive` FROM $newasviewon".(empty($newviewlimit)?"":" WHERE $newviewlimit");
            }
            $result = $DB->query($query);
         }
         else {
            $entities = ($item->input['is_entity_limited']?"`entities_id` INT(11) UNSIGNED NOT NULL DEFAULT 0,":""); // with or without 'entities_id' column
            if (!$newistreedropdown) {
               // new simple dropdown table
               $query = "CREATE TABLE IF NOT EXISTS `".$newtablename."` (
                  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,".
                  $entities.
                  "`name` VARCHAR(45) NOT NULL,
                  `comment` VARCHAR(255) NULL,
                  `completename` MEDIUMTEXT NULL,
                  PRIMARY KEY (`id`) ,
                  UNIQUE INDEX `".$newtablename."_name` (`name`) )
                  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
            $result = $DB->query($query);
            } 
            else { // new treedropdon table
               $query = "CREATE TABLE IF NOT EXISTS `".$newtablename."` (
                  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,".
                  $entities.
                  "`is_recursive` BIT NOT NULL DEFAULT 0,
                  `name` VARCHAR(45) NOT NULL,
                  $newfieldname INT(11) UNSIGNED NOT NULL DEFAULT 0,
                  `completename` MEDIUMTEXT NULL,
                  `comment` VARCHAR(255) NULL,
                  `level` INT NOT NULL DEFAULT 0,
                  `sons_cache` LONGTEXT NULL,
                  `ancestors_cache` LONGTEXT NULL,
                  PRIMARY KEY (`id`) ,
                  UNIQUE INDEX `".$newtablename."_name` (`name`) )
                  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
            $result = $DB->query($query);
            }
         }
      }
      create_plugin_archibp_classfiles($dir, $newclassname, $newistreedropdown);
   }
   if (substr($oldclassname, 0, 13) == 'PluginArchibp'
   && $oldclassname != $newclassname) {
      //old dropdown was owned by this plugin -> drop table or view if it hasn't been renamed
      $oldrootname = strtolower(substr($oldclassname, 13));
      $oldfilename = $oldrootname;
      $oldtablename = 'glpi_plugin_archibp_'.getPlural($oldrootname);
      $oldfieldname = 'plugin_archibp_'.getPlural($oldrootname).'_id';
      $tableorview = empty($oldasviewon)?"TABLE":"VIEW";
      $query = "DROP $tableorview IF EXISTS `".$oldtablename."`";
      $result = $DB->query($query);
      $query = "DELETE FROM `glpi_plugin_archibp_configbplinks` WHERE `name` = '".$oldclassname."'";
      $result = $DB->query($query);
      // delete files in inc and front directories
      if (file_exists($dir.'/inc/'.$oldfilename.'.class.php')) 
         unlink($dir.'/inc/'.$oldfilename.'.class.php');
      if (file_exists($dir.'/front/'.$oldfilename.'.form.php')) 
         unlink($dir.'/front/'.$oldfilename.'.form.php');
      if (file_exists($dir.'/front/'.$oldfilename.'.php')) 
         unlink($dir.'/front/'.$oldfilename.'.php');
   }
}
function hook_pre_item_purge_archibp_configbplink(CommonDBTM $item) {
   global $DB;
   $dir = Plugin::getPhpDir("archibp", true);
   $oldclassname = $item->fields['name'];
   $oldasviewon = $item->fields['as_view_on'];
   $oldrootname = strtolower(substr($oldclassname, 13));
   $oldfilename = $oldrootname;
   $oldid = $item->fields['id'];
   // suppress in glpi_plugin_archibp_configbps
   $query = "UPDATE `glpi_plugin_archibp_configbps` SET `plugin_archibp_configbplinks_id` = 0 WHERE `plugin_archibp_configbplinks_id` = '".$oldid."'";
   $result = $DB->query($query);
   if (substr($oldclassname, 0, 13) == 'PluginArchibp') {
      $oldtablename = 'glpi_plugin_archibp_'.getPlural($oldrootname);
      $oldfieldname = 'plugin_archibp_'.getPlural($oldrootname).'_id';
      $tableorview = empty($oldasviewon)?"TABLE":"VIEW";
      $query = "DROP $tableorview IF EXISTS `".$oldtablename."`";
      $result = $DB->query($query);
      // delete files in inc and front directories
      if (file_exists($dir.'/inc/'.$oldfilename.'.class.php')) 
         unlink($dir.'/inc/'.$oldfilename.'.class.php');
      if (file_exists($dir.'/front/'.$oldfilename.'.form.php')) 
         unlink($dir.'/front/'.$oldfilename.'.form.php');
      if (file_exists($dir.'/front/'.$oldfilename.'.php')) 
         unlink($dir.'/front/'.$oldfilename.'.php');
   }
   return true;
}
function create_plugin_archibp_classfiles($dir, $newclassname, $istreedropdown = false) {
   if (substr($newclassname, 0, 13) == 'PluginArchibp') {
      $newfilename = strtolower(substr($newclassname, 13));
      $dropdowntype = 'CommonDropdown';
      if ($istreedropdown) $dropdowntype = 'CommonTreeDropdown';
      // create files in inc and front directories, with read/write access
      file_put_contents($dir.'/inc/'.$newfilename.'.class.php', 
      "<?php
/*
 -------------------------------------------------------------------------
 Archibp plugin for GLPI
 Copyright (C) 2009-2023 by Eric Feron.
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
      if (!defined('GLPI_ROOT')) {
         die('Sorry. You cannott access directly to this file');
      }
      class $newclassname extends $dropdowntype {
      }
      ?>");
      file_put_contents($dir.'/front/'.$newfilename.'.form.php', 
      "<?php
/*
 -------------------------------------------------------------------------
 Archibp plugin for GLPI
 Copyright (C) 2009-2023 by Eric Feron.
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
      include ('../../../inc/includes.php');
      \$dropdown = new $newclassname();
      include (GLPI_ROOT . '/front/dropdown.common.form.php');
      ?>");
      file_put_contents($dir.'/front/'.$newfilename.'.php', 
      "<?php
/*
 -------------------------------------------------------------------------
 Archibp plugin for GLPI
 Copyright (C) 2009-2023 by Eric Feron.
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
      include ('../../../inc/includes.php');
      \$dropdown = new $newclassname();
      include (GLPI_ROOT . '/front/dropdown.common.php');
      ?>");
      chmod($dir.'/inc/'.$newfilename.'.class.php', 0660);
      chmod($dir.'/front/'.$newfilename.'.form.php', 0660);
      chmod($dir.'/front/'.$newfilename.'.php', 0660);
      // refresh with new files
//      header("Refresh:0");
//   Session::addMessageAfterRedirect(__('Please, refresh the display', 'archibp'));
   }
   return true;
}
?>
