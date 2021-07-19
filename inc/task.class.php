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

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginArchibpTask extends CommonTreeDropdown {

   public $dohistory=true;
   static $rightname = "plugin_archibp";
   protected $usenotepad         = true;
   
   static $types = ['Group', 
					'PluginArchidataDataelement',
					'PluginArchifunFuncarea',
					'PluginArchiswSwcomponent'];

   static function getTypeName($nb=0) {

      return _n('Business Process', 'Business Processes', $nb, 'archibp');
   }

   function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {

   switch ($item->getType()) {
        case 'Group' :
			if ($_SESSION['glpishow_count_on_tabs']) {
				return self::createTabEntry(self::getTypeName(2), self::countForItem($item));
			}
			return self::getTypeName(2);
        case 'PluginArchibpTask' :
			return $this->getTypeName(Session::getPluralNumber());
      }
      return '';
   }


   static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {

      switch ($item->getType()) {
        case 'Group' :
			$self = new self();
			$self->showPluginFromSupplier($item->getField('id'));
            break;
        case 'PluginArchibpTask' :
            $item->showChildren();
            break;
      }
      return true;
   }

   static function countForItem(CommonDBTM $item) {

      $dbu = new DbUtils();
      return $dbu->countElementsInTable('glpi_plugin_archibp_tasks'/*,
                                  "`suppliers_id` = '".$item->getID()."'"*/);
   }

   //clean if task are deleted
   function cleanDBonPurge() {

//      $temp = new PluginArchibpTask_Item();
//      $temp->deleteByCriteria(['plugin_archibp_tasks_id' => $this->fields['id']]);
   }

   // search fields from GLPI 9.3 on
   function rawSearchOptions() {

      $tab = [];
      if (version_compare(GLPI_VERSION,'9.2','le')) return $tab;

      $tab[] = [
         'id'   => 'common',
         'name' => self::getTypeName(2)
      ];

      $tab[] = [
         'id'            => '1',
         'table'         => $this->getTable(),
         'field'         => 'name',
         'name'          => __('Name'),
         'datatype'      => 'itemlink',
         'itemlink_type' => $this->getType()
      ];

      $tab[] = [
         'id'       => '2',
         'table'    => $this->getTable(),
         'field'    => 'level',
         'name'     => __('Level'),
         'datatype' => 'text'
      ];

      $tab[] = [
         'id'            => '4',
         'table'         => $this->getTable(),
         'field'    => 'description',
         'name'     => __('Description'),
         'datatype' => 'text'
      ];

      $tab[] = [
         'id'        => '11',
         'table'     => 'glpi_users',
         'field'     => 'name',
         'linkfield' => 'users_id',
         'name'      => __('Task Expert', 'archibp'),
         'datatype'  => 'dropdown',
         'right'     => 'interface'
      ];

      $tab[] = [
         'id'        => '12',
         'table'     => 'glpi_groups',
         'field'     => 'name',
         'linkfield' => 'groups_id',
         'name'      => __('Task Follow-up', 'archibp'),
         'condition' => '`is_assign`',
         'datatype'  => 'dropdown'
      ];

      $tab[] = [
         'id'            => '16',
         'table'         => $this->getTable(),
         'field'         => 'date_mod',
         'massiveaction' => false,
         'name'          => __('Last update'),
         'datatype'      => 'datetime'
      ];

      $tab[] = [
         'id'            => '72',
         'table'         => $this->getTable(),
         'field'         => 'id',
         'name'          => __('ID'),
         'datatype'      => 'number'
      ];

      $tab[] = [
         'id'       => '80',
         'table'    => $this->getTable(),
         'field'    => 'completename',
         'name'     => __('Tasks Structure','archibp'),
         'datatype' => 'dropdown'
      ];

      $tab[] = [
         'id'    => '81',
         'table' => 'glpi_entities',
         'field' => 'entities_id',
         'name'  => __('Entity') . "-" . __('ID')
      ];

      return $tab;
   }

   //define header form
   function defineTabs($options=[]) {

      $ong = [];
      $this->addDefaultFormTab($ong);
      $this->addStandardTab('PluginArchibpTask', $ong, $options);
      $this->addStandardTab('PluginArchibpTask_Item', $ong, $options);
      $this->addStandardTab('Notepad', $ong, $options);
      $this->addStandardTab('Log', $ong, $options);

      return $ong;
   }

   /*
    * Return the SQL command to retrieve linked object
    *
    * @return a SQL command which return a set of (itemtype, items_id)
    */
/*   function getSelectLinkedItem () {
      return "SELECT `itemtype`, `items_id`
              FROM `glpi_plugin_archibp_task_items`
              WHERE `plugin_archibp_tasks_id`='" . $this->fields['id']."'";
   }
*/
   function showForm ($ID, $options=[]) {

		// Because a lot of informations, we use 3 (6) columns
		//	 Make <table> aware of it
//		$options['colspan']=4;

	  $this->initForm($ID, $options);
      $this->showFormHeader($options);

      echo "<tr class='tab_bg_1'>";
      //name of task
      echo "<td>".__('Name')."</td>";
      echo "<td>";
      Html::autocompletionTextField($this,"name");
      echo "</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      //completename of task
      echo "<td>".__('As child of').": </td>";
      echo "<td>";
      Dropdown::show('PluginArchibpTask', ['value' => $this->fields["plugin_archibp_tasks_id"]]);
      echo "</td>";
      //level of task
      echo "<td>".__('Level').": </td>";
      echo "<td>";
      Html::autocompletionTextField($this,"level",['size' => "2", 'option' => "readonly='readonly'"]);
      echo "</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      //description of task
      echo "<td>".__('Description').":	</td>";
      echo "<td class='top center' colspan='5'>";
      Html::autocompletionTextField($this,"description",['option' => 'style="width:100%"']);
      echo "</td>";
      echo "</tr>";
      echo "<tr class='tab_bg_1'>";
      //comment about task
      echo "<td>".__('Comment').":	</td>";
      echo "<td class='top center' colspan='5'><textarea cols='100' rows='5' name='comment' >".$this->fields["comment"]."</textarea>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      //type
      echo "<td>".__('Type').": </td><td>";
	      Dropdown::show('PluginArchibpTaskType', ['value' => $this->fields["plugin_archibp_tasktypes_id"]]);
      echo "</td>";
      //criticity
      echo "<td>".__('Criticity', 'archibp').": </td><td>";
      Dropdown::show('PluginArchibpCriticity', ['name' => "plugin_archibp_criticities_id", 'value' => $this->fields["plugin_archibp_criticities_id"]]);
      echo "</td>";
      echo "</tr>";
      
      echo "<tr class='tab_bg_1'>";
      //application
      echo "<td>".__('Linked to application', 'archibp').": </td><td>";
      Dropdown::show('PluginArchibpSwcomponent', ['name' => "plugin_archibp_swcomponents_id", 'value' => $this->fields["plugin_archibp_swcomponents_id"],'entity' => $this->fields["entities_id"]]);
      echo "</td>";
      //transaction code
      echo "<td>".__('Transaction code', 'archibp').": </td>";
      echo "<td>";
      Html::autocompletionTextField($this,"transactioncode",['size' => "100"]);
      echo "</td>";
      echo "</tr>";

		echo "<tr class='tab_bg_1'>";
		echo "<td>";
		echo Html::link(__('URL doc.', 'archibp'), $this->fields["address"]);
		echo "</td>";
		echo "<td colspan='3'>";
		Html::autocompletionTextField($this, "address", ['option' => 'style="width:100%"']);
		echo "</td>";
		echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      //groups
      echo "<td>".__("Task Owner's group", 'archibp')."</td><td>";
      Group::dropdown(['name'      => 'groups_id', 
                        'value'     => $this->fields['groups_id'], 
                        'entity'    => $this->fields['entities_id'], 
                        'condition' => ['is_assign' => 1]
                        ]);
      echo "</td>";
      //users
      echo "<td>".__('Task Expert', 'archibp')."</td><td>";
      User::dropdown(['name' => "users_id", 'value' => $this->fields["users_id"], 'entity' => $this->fields["entities_id"], 'right' => 'interface']);
      echo "</td>";
      echo "</tr>";



      $this->showFormButtons($options);

      return true;
   }
   
   /**
    * Make a select box for link dataflow
    *
    * Parameters which could be used in options array :
    *    - name : string / name of the select (default is plugin_dataflows_dataflowtypes_id)
    *    - entity : integer or array / restrict to a defined entity or array of entities
    *                   (default -1 : no restriction)
    *    - used : array / Already used items ID: not to display in dropdown (default empty)
    *
    * @param $options array of possible options
    *
    * @return nothing (print out an HTML select box)
   **/
   static function dropdownTask($options=[]) {
      global $DB, $CFG_GLPI;


      $p['name']    = 'plugin_archibp_tasks_id';
      $p['entity']  = '';
      $p['used']    = [];
      $p['display'] = true;

      if (is_array($options) && count($options)) {
         foreach ($options as $key => $val) {
            $p[$key] = $val;
         }
      }

      $where = " WHERE `glpi_plugin_archibp_tasks`.`is_deleted` = '0' ".
                       getEntitiesRestrictRequest("AND", "glpi_plugin_archibp_tasks", '', $p['entity'], true);

      $p['used'] = array_filter($p['used']);
      if (count($p['used'])) {
         $where .= " AND `id` NOT IN (0, ".implode(",",$p['used']).")";
      }

      $query = "SELECT *
                FROM `glpi_plugin_archibp_tasktypes`
                WHERE `id` IN (SELECT DISTINCT `plugin_archibp_tasktypes_id`
                               FROM `glpi_plugin_archibp_tasks`
                             $where)
                ORDER BY `name`";
      $result = $DB->query($query);

      $values = [0 => Dropdown::EMPTY_VALUE];

      while ($data = $DB->fetchAssoc($result)) {
         $values[$data['id']] = $data['name'];
      }
      $rand = mt_rand();
      $out  = Dropdown::showFromArray('_tasktype', $values, ['width'   => '30%',
                                                                     'rand'    => $rand,
                                                                     'display' => false]);
      $field_id = Html::cleanId("dropdown__tasktype$rand");

      $params   = ['tasktype' => '__VALUE__',
                        'entity' => $p['entity'],
                        'rand'   => $rand,
                        'myname' => $p['name'],
                        'used'   => $p['used']];

      $out .= Ajax::updateItemOnSelectEvent($field_id,"show_".$p['name'].$rand,
                                            $CFG_GLPI["root_doc"]."/plugins/archibp/ajax/dropdownTypeTasks.php",
                                            $params, false);
      $out .= "<span id='show_".$p['name']."$rand'>";
      $out .= "</span>\n";

      $params['tasktype'] = 0;
      $out .= Ajax::updateItem("show_".$p['name'].$rand,
                               $CFG_GLPI["root_doc"]. "/plugins/archibp/ajax/dropdownTypeTasks.php",
                               $params, false);
      if ($p['display']) {
         echo $out;
         return $rand;
      }
      return $out;
   }

   /**
    * For other plugins, add a type to the linkable types
    *
    * @since version 1.3.0
    *
    * @param $type string class name
   **/
   static function registerType($type) {
      if (!in_array($type, self::$types)) {
         self::$types[] = $type;
      }
   }


   /**
    * Type than could be linked to a Rack
    *
    * @param $all boolean, all type, or only allowed ones
    *
    * @return array of types
   **/
   static function getTypes($all=false) {

      if ($all) {
         return self::$types;
      }

      // Only allowed types
      $types = self::$types;

      foreach ($types as $key => $type) {
         if (!class_exists($type)) {
            continue;
         }

         $item = new $type();
         if (!$item->canView()) {
            unset($types[$key]);
         }
      }
      return $types;
   }


}

?>
