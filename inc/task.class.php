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
//					'PluginArchidataDataelement',
					'PluginArchifunFuncarea',
//					'PluginArchibpSwcomponent'
                    ];

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
   global $DB;

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

      $linktable = [];
      $tablequery = "SELECT * FROM `glpi_plugin_archibp_configbplinks`";
      $tableresult = $DB->query($tablequery);
      while ($tabledata = $DB->fetchAssoc($tableresult)) {
         $linktable[$tabledata['id']]['name'] = $tabledata['name'];
         $linktable[$tabledata['id']]['has_dropdown'] = $tabledata['has_dropdown'];
         $linktable[$tabledata['id']]['is_entity_limited'] = $tabledata['is_entity_limited'];
      }

      $datatypetable = [];
      $datatypequery = "SELECT * FROM `glpi_plugin_archibp_configbpdatatypes`";
      $datatyperesult = $DB->query($datatypequery);
      while ($datatypedata = $DB->fetchAssoc($datatyperesult)) {
         $datatypetable[$datatypedata['id']]['name'] = $datatypedata['name'];
      }

      $fieldquery = "SELECT * 
                FROM `glpi_plugin_archibp_configbps` 
                WHERE `is_deleted` = 0 
                ORDER BY `id`";
      $fieldresult = $DB->query($fieldquery);
      $rowcount = $DB->numrows($fieldresult);
      $tabid = 1; // tabid 1 is used for name
      $tabtable = $this->getTable();
      while ($fielddata = $DB->fetchAssoc($fieldresult)) {
         $tabid = 1 + $fielddata['id'];
         $datatypeid = $fielddata['plugin_archibp_configbpdatatypes_id'];
         switch($datatypeid) {
            case 1: //Text
            case 2: //Boolean
            case 3: //Date
            case 4: //Date and time
            case 5: //Number
            case 8: //Textarea
               $tab[] = [
                  'id'       => $tabid,
                  'table'    => $tabtable,
                  'field'    => $fielddata['name'],
                  'name'     => __($fielddata['description'],'archibp'),
                  'datatype' => $datatypetable[$datatypeid]['name'],
                  'massiveaction' => $fielddata['massiveaction'],
                  'nosearch' => $fielddata['nosearch']
               ];
               break;
            case 6: //Dropdown
               $linktableid = $fielddata['plugin_archibp_configbplinks_id'];
               $itemtype = $linktable[$linktableid]['name'];
               $tablename = $this->getTable($itemtype);
               $tab[] = [
                  'id'       => $tabid,
                  'table'    => $tablename,
                  'field'    => 'name',
                  'name'     => __($fielddata['description'],'archibp'),
                  'datatype' => $datatypetable[$datatypeid]['name']
               ];
               break;
            case 7: //Itemlink
               break;
         }
      }

      $tab[] = [
         'id'            => ++$tabid,
         'table'         => $this->getTable(),
         'field'         => 'id',
         'name'          => __('ID'),
         'datatype'      => 'number'
      ];

      $tab[] = [
         'id'       => ++$tabid,
         'table'    => $this->getTable(),
         'field'    => 'completename',
         'name'     => __('Tasks Structure','archibp'),
         'datatype' => 'dropdown'
      ];

      $tab[] = [
         'id'    => ++$tabid,
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
      $this->addImpactTab($ong, $options);
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
   global $DB;

		// Because a lot of informations, we use 3 (6) columns
		//	 Make <table> aware of it
      // check whether there are "center" columns
      $columnquery = "SELECT * 
                FROM `glpi_plugin_archibp_configbps` 
                WHERE `is_deleted` = 0 AND `plugin_archibp_configbphaligns_id` in (3,4,5)";
      $columnresult = $DB->query($columnquery);
      $rowcount = $DB->numrows($columnresult);
      if ($rowcount == 0) {
         $columncount = 4;
      } else {
         $columncount = 4;
      }

//      $options['colspan'] = $columncount;
	  $this->initForm($ID, $options);
      $this->showFormHeader($options);

      // Line: 1
      $curline = 1;
      echo "<tr class='tab_bg_1'>";
      //name of task
      echo "<td>".__('Name')."</td>";
      echo "<td>";
      echo Html::input('name',['value' => $this->fields['name'], 'id' => "name"]);
      echo "</td>";

      // Line: 2
      $curline++;
      echo "<tr class='tab_bg_1'>";
      //completename of task
      echo "<td>".__('As child of').": </td>";
      echo "<td>";
      Dropdown::show('PluginArchibpTask', ['value' => $this->fields["plugin_archibp_tasks_id"]]);
      echo "</td>";
      $halign = 3;
/*      //level of task
      echo "<td>".__('Level').": </td>";
      echo "<td>";
      echo Html::input('level',['value' => $this->fields['level'], 'id' => "level" , 'size' => 2, 'readonly' => true]);
      echo "</td>";
      echo "</tr>";

      // Line: 3
      echo "<tr class='tab_bg_1'>";
      //description of task
      echo "<td>".__('Description').":	</td>";
      echo "<td class='top center' colspan='5'>";
      echo Html::input('description',['value' => $this->fields['description'], 'id' => "description" , 'width' => "100%"]);
      echo "</td>";
      echo "</tr>";
      echo "<tr class='tab_bg_1'>";
      //comment about task
      echo "<td>".__('Comment').":	</td>";
      echo "<td class='top center' colspan='5'><textarea cols='100' rows='5' name='comment' >".$this->fields["comment"]."</textarea>";
      echo "</tr>";

      // Line: 4
      echo "<tr class='tab_bg_1'>";
      //type
      echo "<td>".__('Type').": </td><td>";
	      Dropdown::show('PluginArchibpTasktype', ['value' => $this->fields["plugin_archibp_tasktypes_id"]]);
      echo "</td>";
      //criticity
      echo "<td>".__('Criticity', 'archibp').": </td><td>";
      Dropdown::show('PluginArchibpCriticity', ['name' => "plugin_archibp_criticities_id", 'value' => $this->fields["plugin_archibp_criticities_id"]]);
      echo "</td>";
      echo "</tr>";
      
      // Line: 5
      echo "<tr class='tab_bg_1'>";
	      //target
	      echo "<td>".__('Targets','archibp')."</td>";
		   echo "<td>";
	      Dropdown::show('PluginArchibpTasktarget', ['value' => $this->fields["plugin_archibp_tasktargets_id"]]);
	      echo "</td>";
	      //status
	      echo "<td>".__('Status','archibp')."</td>";
		   echo "<td>";
	      Dropdown::show('PluginArchibpTaskstate', ['value' => $this->fields["plugin_archibp_taskstates_id"]]);
	      echo "</td>";
      echo "</tr>";

      // Line: 6
      echo "<tr class='tab_bg_1'>";
      //application
      echo "<td>".__('Linked to application', 'archibp').": </td><td>";
      Dropdown::show('PluginArchibpSwcomponent', ['name' => "plugin_archibp_swcomponents_id", 'value' => $this->fields["plugin_archibp_swcomponents_id"],'entity' => $this->fields["entities_id"]]);
      echo "</td>";
      //transaction code
      echo "<td>".__('Transaction code', 'archibp').": </td>";
      echo "<td>";
      echo Html::input('transactioncode',['value' => $this->fields['transactioncode'], 'id' => "transactioncode" , 'width' => "100%"]);
      echo "</td>";
      echo "</tr>";

      // Line: 7
		echo "<tr class='tab_bg_1'>";
		echo "<td>";
		echo Html::link(__('URL doc.', 'archibp'), $this->fields["address"]);
		echo "</td>";
		echo "<td colspan='3'>";
        echo Html::input('address',['value' => $this->fields['address'], 'id' => "address" , 'width' => "100%"]);
		echo "</td>";
		echo "</tr>";

      // Line: 8
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
*/
      $linktable = [];
      $tablequery = "SELECT * FROM `glpi_plugin_archibp_configbplinks`";
      $tableresult = $DB->query($tablequery);
      while ($tabledata = $DB->fetchAssoc($tableresult)) {
         $linktable[$tabledata['id']]['name'] = $tabledata['name'];
         $linktable[$tabledata['id']]['has_dropdown'] = $tabledata['has_dropdown'];
         $linktable[$tabledata['id']]['is_entity_limited'] = $tabledata['is_entity_limited'];
      }

      $fieldquery = "SELECT * 
                FROM `glpi_plugin_archibp_configbps` 
                WHERE `is_deleted` = 0 AND `plugin_archibp_configbpfieldgroups_id` = 0 
                ORDER BY `row`, `plugin_archibp_configbphaligns_id`";
      $fieldresult = $DB->query($fieldquery);
      $rowcount = $DB->numrows($fieldresult);
      if ($rowcount > 0) {
         $fgroupname = '';
         $rownbr = $curline;
//         $halign = 5;
         $tonextrow = false;
         while ($fielddata = $DB->fetchAssoc($fieldresult)) {
            $fieldtype = $fielddata['plugin_archibp_configbphaligns_id'];
            if ($fielddata['row'] != $rownbr) {
               if ($rownbr != $curline) {
                  // If not the first row, end preceding table row
                  echo "</tr>";
               }
               // Set current rownbr
               $rownbr = $fielddata['row'];
               // Start new table row
               echo "<tr class='tab_bg_1'>";
               $halign = 1;
               $tonextrow = false;
            } else if ($tonextrow) {
               continue; // skip this field which is located on the same row (and should not)
            }
            
            //Display field
               switch($fieldtype) {
               case 1: // Full row
                  if ($halign == 1) {
                     $colspan = $columncount - 1;
                     $this->displayField($fielddata, $colspan, $linktable);
                     $halign += 1 + $colspan; // move halign to next column to write into
                     $tonextrow = true;
                  }
                  break;
               case 2: // Left column
                  if ($halign == 1) {
                     $colspan = 1;
                     $this->displayField($fielddata, $colspan, $linktable);
                     $halign += 1 + $colspan; // move halign to next column to write into
                     $tonextrow = false;
                  }
                  break;
               case 3: // Left+Center columns
                  if ($halign == 1) {
                     $colspan = 3;
                    $this->displayField($fielddata, $colspan, $linktable);
                     $halign += 1 + $colspan; // move halign to next column to write into
                     $tonextrow = true;
                  }
                  break;
               case 4: // Center column
                  if ($halign <= 3) {
                     $colspan = 1;
                     while ($halign < 3) { // fill empty columns
                        echo "<td/>";
                        $halign++;
                     }
                     $this->displayField($fielddata, $colspan, $linktable);
                     $halign += 1 + $colspan; // move halign to next column to write into
                     $tonextrow = false;
                  }
                  break;
               case 5: // Center+Right columns
                  if ($halign <= 3) {
                     $colspan = 3;
                     while ($halign < 3) { // fill empty columns
                        echo "<td/>";
                        $halign++;
                     }
                     $this->displayField($fielddata, $colspan, $linktable);
                     $halign += 1 + $colspan; // move halign to next column to write into
                     $tonextrow = true;
                  }
                  break;
               case 6: // Right column
                  if ($halign <= $columncount - 1) {
                     $colspan = 1;
                     while ($halign < $columncount - 1) { // fill empty columns
                        echo "<td/>";
                        $halign++;
                     }
                     $this->displayField($fielddata, $colspan, $linktable);
                     $halign += 1 + $colspan; // move halign to next column to write into
                     $tonextrow = false;
                  }
                  break;
               }
            }
            // End last table row
            echo "</tr>";
      }

      // Generate accordions according to groups named in configbpfieldgroups

      $fgroupquery = "SELECT * 
                FROM `glpi_plugin_archibp_configbpfieldgroups` 
                ORDER BY `sortorder`";
      $fgroupresult = $DB->query($fgroupquery);

      while ($fgroupdata = $DB->fetchAssoc($fgroupresult)) {
         $fgroupid = $fgroupdata['id'];
         $fgroupname = $fgroupdata['name']."tbl"; //name of the grouping table
         $fgroupcomment = $fgroupdata['comment'];
         $fgroupexpanded = ($fgroupdata['is_visible'] != 0)?'collapse show':'collapse';

         $fieldquery = "SELECT * 
                FROM `glpi_plugin_archibp_configbps` 
                WHERE `is_deleted` = 0 AND `plugin_archibp_configbpfieldgroups_id` = $fgroupid 
                ORDER BY `row`, `plugin_archibp_configbphaligns_id`";
         $fieldresult = $DB->query($fieldquery);
         $rowcount = $DB->numrows($fieldresult);
         if ($rowcount > 0) {
            // Accordion separator
            echo "<tr class='badge accordion-header'><td><button class='accordion-button' type='button' data-bs-toggle='collapse' data-bs-target='.".$fgroupname."'>".$fgroupcomment."</button></td></tr>";

            $rownbr = '';
            while ($fielddata = $DB->fetchAssoc($fieldresult)) {
               if ($fielddata['row'] != $rownbr) {
                  if ($rownbr != '') {
                     // If not the first row, end preceding table row
                     echo "</tr>";
                  }
                  // Set current rownbr
                  $rownbr = $fielddata['row'];
                  // Start new table row
                  echo "<tr class='tab_bg_1 ".$fgroupname." accordion-collapse  ".$fgroupexpanded."'>";
                  $halign = 1;
                  $tonextrow = false;
               } else if ($tonextrow) {
                  continue; // skip this field which is located on the same row (and should not)
               }
            
               //Display field
               $fieldtype = $fielddata['plugin_archibp_configbphaligns_id'];
               switch($fieldtype) {
               case 1: // Full row
                  if ($halign == 1) {
                     $colspan = $columncount - 1;
                     $this->displayField($fielddata, $colspan, $linktable);
                     $halign += 1 + $colspan; // move halign to next column to write into
                     $tonextrow = true;
                  }
                  break;
               case 2: // Left column
                  if ($halign == 1) {
                     $colspan = 1;
                     $this->displayField($fielddata, $colspan, $linktable);
                     $halign += 1 + $colspan; // move halign to next column to write into
                     $tonextrow = false;
                  }
                  break;
               case 3: // Left+Center columns
                  if ($halign == 1) {
                     $colspan = 3;
                     $this->displayField($fielddata, $colspan, $linktable);
                     $halign += 1 + $colspan; // move halign to next column to write into
                     $tonextrow = true;
                  }
                  break;
               case 4: // Center column
                  if ($halign <= 3) {
                     $colspan = 1;
                     while ($halign < 3) { // fill empty columns
                        echo "<td/>";
                        $halign++;
                     }
                     $this->displayField($fielddata, $colspan, $linktable);
                     $halign += 1 + $colspan; // move halign to next column to write into
                     $tonextrow = false;
                  }
                  break;
               case 5: // Center+Right columns
                  if ($halign <= 3) {
                     $colspan = 3;
                     while ($halign < 3) { // fill empty columns
                        echo "<td/>";
                        $halign++;
                     }
                     $this->displayField($fielddata, $colspan, $linktable);
                     $halign += 1 + $colspan; // move halign to next column to write into
                     $tonextrow = true;
                  }
                  break;
               case 6: // Right column
                  if ($halign <= $columncount - 1) {
                     $colspan = 1;
                     while ($halign < $columncount - 1) { // fill empty columns
                        echo "<td/>";
                        $halign++;
                     }
                     $this->displayField($fielddata, $colspan, $linktable);
                     $halign += 1 + $colspan; // move halign to next column to write into
                     $tonextrow = false;
                  }
                  break;
               }
            }
            // End last table row
            echo "</tr>";
         }
      }


      $this->showFormButtons($options);

      return true;
   }
   
   function displayField($fielddata, $colspan = 1, $linktable=[]) {
      $fieldname = $fielddata['name'];
      $fielddescription = $fielddata['description'];
      $fieldreadonly = $fielddata['is_readonly']?'true':'false';
      $params = [];
      $params['value'] = $this->fields[$fieldname];
      if ($fielddata['is_readonly']) {
         $params['readonly'] = 'true';
      }
      switch($fielddata['plugin_archibp_configbpdatatypes_id']) {
         case 1: //Text
            echo "<td>".__($fielddescription, 'archibp')."</td>";
            echo "<td colspan='".$colspan."'>";
            $params['id'] = $fieldname;
            $params['width'] = '100%';
            echo Html::input($fieldname,$params);
            echo "</td>";
            break;
         case 2: //Boolean
            echo "<td>".__($fielddescription, 'archibp')."</td>";
            echo "<td colspan='".$colspan."'>";
            Dropdown::showYesNo($fieldname,$this->fields[$fieldname], -1);
            echo "</td>";
            break;
         case 3: //Date
            echo "<td>".__($fielddescription, 'archibp')."</td>";
            echo "<td colspan='".$colspan."'>";
            Html::showDateField($fieldname, ['value' => empty($this->fields[$fieldname])?date("Y-m-d"):$this->fields[$fieldname], 'readonly' => $fieldreadonly]);
            echo "</td>";
            break;
         case 4: //Date and time
            echo "<td>".__($fielddescription, 'archibp')."</td>";
            echo "<td colspan='".$colspan."'>";
            Html::showDateTimeField($fieldname, ['value' => empty($this->fields[$fieldname])?date("Y-m-d H:i:s"):$this->fields[$fieldname], 'readonly' => $fieldreadonly]);
            echo "</td>";
            break;
         case 5: //Number
            echo "<td>".__($fielddescription, 'archibp')."</td>";
            echo "<td colspan='".$colspan."'>";
            Dropdown::showNumber($fieldname, $params);
            echo "</td>";
            break;
         case 6: //Dropdown
         case 9: //Dropdown
            if ($linktable[$fielddata['plugin_archibp_configbplinks_id']]['is_entity_limited']) {
               $params['entity'] = $this->fields["entities_id"];
            }
            echo "<td>".__($fielddescription, 'archibp')."</td>";
            echo "<td colspan='".$colspan."'>";
            if ($linktable[$fielddata['plugin_archibp_configbplinks_id']]['has_dropdown']) {
               $linktable[$fielddata['plugin_archibp_configbplinks_id']]['name']::dropdown($params);
            }
            else {
               Dropdown::show($linktable[$fielddata['plugin_archibp_configbplinks_id']]['name'], $params);
            }
            echo "</td>";
            break;
         case 7: //Itemlink
            echo "<td>";
            echo Html::link(__($fielddescription, 'archibp'), $this->fields[$fieldname]);
            echo "</td>";
            echo "<td colspan='".$colspan."'>";
            $params['id'] = $fieldname;
            $params['width'] = '100%';
            echo Html::input($fieldname,$params);
            echo "</td>";
            break;
         case 8: //Textarea
            echo "<td>".__($fielddescription, 'archibp')."</td>";
            echo "<td colspan='".$colspan."'>";
            echo Html::textarea(['name' => $fieldname, 'value' => $this->fields[$fieldname], 'editor_id' => $fieldname, 
                                'enable_richtext' => true, 'display' => false, 'rows' => 3, 'readonly' => $fieldreadonly]);
            echo "</td>";
            break;      
      }
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
                                            Plugin::getWebDir("archibp")."/ajax/dropdownTypeArchibp.php",
                                            $params, false);
      $out .= "<span id='show_".$p['name']."$rand'>";
      $out .= "</span>\n";

      $params['tasktype'] = 0;
      $out .= Ajax::updateItem("show_".$p['name'].$rand,
                               Plugin::getWebDir("archibp")."/ajax/dropdownTypeArchibp.php",
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
