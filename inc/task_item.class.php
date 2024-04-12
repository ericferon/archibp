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

class PluginArchibpTask_Item extends CommonDBRelation {

   // From CommonDBRelation
   static public $itemtype_1 = "PluginArchibpTask";
   static public $items_id_1 = 'plugin_archibp_tasks_id';
   static public $take_entity_1 = false ;
    
   static public $itemtype_2 = 'itemtype';
   static public $items_id_2 = 'items_id';
   static public $take_entity_2 = true ;
   
   static $rightname = "plugin_archibp";

   
   /*static function getTypeName($nb=0) {

      if ($nb > 1) {
         return _n('Task item', 'Tasks items', 2, 'archibp');
      }
      return _n('Task item', 'Tasks items', 1, 'archibp');
   }*/

   /**
    * Clean table when item is purged
    *
    * @param $item Object to use
    *
    * @return nothing
    **/
   public static function cleanForItem(CommonDBTM $item) {

      $temp = new self();
      $temp->deleteByCriteria(
         ['itemtype' => $item->getType(),
               'items_id' => $item->getField('id')]
      );
   }

   /**
    * Get Tab Name used for itemtype
    *
    * NB : Only called for existing object
    *      Must check right on what will be displayed + template
    *
    * @since version 0.83
    *
    * @param $item            CommonDBTM object for which the tab need to be displayed
    * @param $withtemplate    boolean  is a template object ? (default 0)
    *
    *  @return string tab name
    **/
   public function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {

      if (!$withtemplate) {
         if ($item->getType()=='PluginArchibpTask'
             && count(PluginArchibpTask::getTypes(false))) {
            if ($_SESSION['glpishow_count_on_tabs']) {
               return self::createTabEntry(_n('Associated item','Associated items',2), self::countForTask($item));
            }
            return _n('Associated item','Associated items',2);

         } else if (in_array($item->getType(), PluginArchibpTask::getTypes(true))
                    && Session::haveRight('plugin_archibp', READ)) {
            if ($_SESSION['glpishow_count_on_tabs']) {
               return self::createTabEntry(PluginArchibpTask::getTypeName(2), self::countForItem($item));
            }
            return PluginArchibpTask::getTypeName(2);
         }
      }
      return '';
   }

   /**
    * show Tab content
    *
    * @since version 0.83
    *
    * @param $item                  CommonGLPI object for which the tab need to be displayed
    * @param $tabnum       integer  tab number (default 1)
    * @param $withtemplate boolean  is a template object ? (default 0)
    *
    * @return true
    **/
   public static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {

      if ($item->getType()=='PluginArchibpTask') {

         self::showForTask($item);

      } else if (in_array($item->getType(), PluginArchibpTask::getTypes(true))) {

         self::showForITem($item);
      }
      return true;
   }

   static function countForTask(PluginArchibpTask $item) {

      $types = implode("','", $item->getTypes());
      if (empty($types)) {
         return 0;
      }
      $dbu = new DbUtils();
      return $dbu->countElementsInTable('glpi_plugin_archibp_tasks_items',
                                        ["plugin_archibp_tasks_id" => $item->getID(),
                                         "itemtype"                      => $item->getTypes()
                                        ]);
   }


   static function countForItem(CommonDBTM $item) {

      $dbu = new DbUtils();
      return $dbu->countElementsInTable('glpi_plugin_archibp_tasks_items',
                                        ["itemtype" => $item->getType(),
                                         "items_id" => $item->getID()]);
   }

   function getFromDBbyTasksAndItem($plugin_archibp_tasks_id,$items_id,$itemtype) {
      global $DB;

      $query = "SELECT * FROM `".$this->getTable()."` " .
         "WHERE `plugin_archibp_tasks_id` = '" . $plugin_archibp_tasks_id . "'
         AND `itemtype` = '" . $items_id . "'
         AND `items_id` = '" . $itemtype . "'";
      if ($result = $DB->query($query)) {
         if ($DB->numrows($result) != 1) {
            return false;
         }
         $this->fields = $DB->fetchAssoc($result);
         if (is_array($this->fields) && count($this->fields)) {
            return true;
         } else {
            return false;
         }
      }
      return false;
   }

   function addItem($values) {

      $this->add(['plugin_archibp_tasks_id'=>$values["plugin_archibp_tasks_id"],
                        'items_id'=>$values["items_id"],
                        'itemtype'=>$values["itemtype"]]);

   }

   function deleteItemByTasksAndItem($plugin_archibp_tasks_id,$items_id,$itemtype) {

      if ($this->getFromDBbyTasksAndItem($plugin_archibp_tasks_id,$items_id,$itemtype)) {
         $this->delete(['id'=>$this->fields["id"]]);
      }
   }

   /**
    * @since version 0.84
   **/
   function getForbiddenStandardMassiveAction() {

      $forbidden   = parent::getForbiddenStandardMassiveAction();
//      $forbidden[] = 'update';
      return $forbidden;
   }
   /**
    * Show items links to a task
    *
    * @since version 0.84
    *
    * @param $task PluginArchibpTask object
    *
    * @return nothing (HTML display)
    **/
   public static function showForTask(PluginArchibpTask $task) {
      global $DB;

      $instID = $task->fields['id'];
      if (!$task->can($instID, READ))   return false;

      $rand=mt_rand();

      $canedit=$task->can($instID, UPDATE);

      $query = "SELECT DISTINCT `itemtype`
             FROM `glpi_plugin_archibp_tasks_items`
             WHERE `plugin_archibp_tasks_id` = '$instID'
             ORDER BY `itemtype`
             LIMIT ".count(PluginArchibpTask::getTypes(true));

      $result = $DB->query($query);
      $number = $DB->numrows($result);

      if (Session::isMultiEntitiesMode()) {
         $colsup=1;
      } else {
         $colsup=0;
      }

      if ($canedit) {
         echo "<div class='firstbloc'>";
         echo "<form method='post' name='archibp_form$rand' id='archibp_form$rand'
         action='".Toolbox::getItemTypeFormURL("PluginArchibpTask")."'>";

         echo "<table class='tab_cadre_fixe'>";
         echo "<tr class='tab_bg_2'><th colspan='".($canedit?(5+$colsup):(4+$colsup))."'>".
            __('Add an item')."</th></tr>";

         echo "<tr class='tab_bg_1'><td colspan='".(3+$colsup)."' class='center'>";
         echo "<input type='hidden' name='plugin_archibp_tasks_id' value='$instID'>";
         Dropdown::showSelectItemFromItemtypes(['items_id_name' => 'items_id',
                                                     'itemtypes'     => PluginArchibpTask::getTypes(true),
                                                     'entity_restrict'
                                                                     => ($task->fields['is_recursive']
                                                        ? getSonsOf('glpi_entities',
                                                                    $task->fields['entities_id'])
                                                        : $task->fields['entities_id']),
                                                     'checkright'
                                                                     => true,
                                               ]);
         echo "</td>";
         echo "<td colspan='2' class='tab_bg_2'>";
         echo "<input type='submit' name='additem' value=\""._sx('button','Add')."\" class='submit'>";
         echo "</td></tr>";
         echo "</table>" ;
         Html::closeForm();
         echo "</div>" ;
      }

      echo "<div class='spaced'>";
      if ($canedit && $number) {
         Html::openMassiveActionsForm('mass'.__CLASS__.$rand);
         $massiveactionparams = [];
         Html::showMassiveActions($massiveactionparams);
      }
      echo "<table class='tab_cadre_fixe'>";
      echo "<tr>";

      if ($canedit && $number) {
         echo "<th width='10'>".Html::getCheckAllAsCheckbox('mass'.__CLASS__.$rand)."</th>";
      }

      echo "<th>".__('Type')."</th>";
      echo "<th>".__('Name')."</th>";
      if (Session::isMultiEntitiesMode())
         echo "<th>".__('Entity')."</th>";
      echo "<th>".__('Serial number')."</th>";
      echo "<th>".__('Inventory number')."</th>";
      echo "</tr>";

      for ($i=0 ; $i < $number ; $i++) {
         $itemType=$DB->result($result, $i, "itemtype");

         if (!($item = getItemForItemtype($itemType))) {
            continue;
         }

         if ($item->canView()) {
            $column="name";
            $itemTable = getTableForItemType($itemType);

            $query = "SELECT `".$itemTable."`.*,
                             `glpi_plugin_archibp_tasks_items`.`id` AS items_id,
                             `glpi_entities`.`id` AS entity "
               ." FROM `glpi_plugin_archibp_tasks_items`, `".$itemTable
               ."` LEFT JOIN `glpi_entities` ON (`glpi_entities`.`id` = `".$itemTable."`.`entities_id`) "
               ." WHERE `".$itemTable."`.`id` = `glpi_plugin_archibp_tasks_items`.`items_id`
                AND `glpi_plugin_archibp_tasks_items`.`itemtype` = '$itemType'
                AND `glpi_plugin_archibp_tasks_items`.`plugin_archibp_tasks_id` = '$instID' "
               . getEntitiesRestrictRequest(" AND ",$itemTable,'','',$item->maybeRecursive());

            if ($item->maybeTemplate()) {
               $query.=" AND `".$itemTable."`.`is_template` = '0'";
            }
            $query.=" ORDER BY `glpi_entities`.`completename`, `".$itemTable."`.`$column`";

            if ($result_linked=$DB->query($query)) {
               if ($DB->numrows($result_linked)) {

                  Session::initNavigateListItems($itemType,PluginArchibpTask::getTypeName(2)." = ".$task->fields['name']);

                  while ($data=$DB->fetchAssoc($result_linked)) {

                     $item->getFromDB($data["id"]);

                     Session::addToNavigateListItems($itemType,$data["id"]);

                     $ID="";

                     if ($_SESSION["glpiis_ids_visible"]||empty($data["name"]))
                        $ID= " (".$data["id"].")";

                     $link=Toolbox::getItemTypeFormURL($itemType);
                     $name= "<a href=\"".$link."?id=".$data["id"]."\">"
                        .$data["name"]."$ID</a>";

                     echo "<tr class='tab_bg_1'>";

                     if ($canedit) {
                        echo "<td width='10'>";
                        Html::showMassiveActionCheckBox(__CLASS__, $data["items_id"]);
                        echo "</td>";
                     }
                     echo "<td class='center'>".$item::getTypeName(1)."</td>";

                     echo "<td class='center' ".(isset($data['is_deleted'])&&$data['is_deleted']?"class='tab_bg_2_2'":"").
                        ">".$name."</td>";

                     if (Session::isMultiEntitiesMode())
                        echo "<td class='center'>".Dropdown::getDropdownName("glpi_entities",$data['entity'])."</td>";

                     echo "<td class='center'>".(isset($data["serial"])? "".$data["serial"]."" :"-")."</td>";
                     echo "<td class='center'>".(isset($data["otherserial"])? "".$data["otherserial"]."" :"-")."</td>";

                     echo "</tr>";
                  }
               }
            }
         }
      }
      echo "</table>";

      if ($canedit && $number) {
         $paramsma['ontop'] =false;
         Html::showMassiveActions($paramsma);
         Html::closeForm();
      }
      echo "</div>";
   }

   /**
   * Show funareas associated to an item
   *
   * @since version 0.84
   *
   * @param $item            CommonDBTM object for which associated tasks must be displayed
   * @param $withtemplate    (default '')
   **/
   static function showForItem(CommonDBTM $item, $withtemplate='') {
      global $DB, $CFG_GLPI;

      $ID = $item->getField('id');

      if ($item->isNewID($ID)) {
         return false;
      }
      if (!Session::haveRight('plugin_archibp', READ)) {
         return false;
      }

      if (!$item->can($item->fields['id'], READ)) {
         return false;
      }

      if (empty($withtemplate)) {
         $withtemplate = 0;
      }

      $canedit       =  $item->canadditem('PluginArchibpTask');
      $rand          = mt_rand();
      $is_recursive  = $item->isRecursive();

      $query = "SELECT `glpi_plugin_archibp_tasks_items`.`id` AS assocID,
                       `glpi_entities`.`id` AS entity,
                       `glpi_plugin_archibp_tasks`.`name` AS assocName,
                       `glpi_plugin_archibp_tasks`.*
                FROM `glpi_plugin_archibp_tasks_items`
                LEFT JOIN `glpi_plugin_archibp_tasks`
                 ON (`glpi_plugin_archibp_tasks_items`.`plugin_archibp_tasks_id`=`glpi_plugin_archibp_tasks`.`id`)
                LEFT JOIN `glpi_entities` ON (`glpi_plugin_archibp_tasks`.`entities_id`=`glpi_entities`.`id`)
                WHERE `glpi_plugin_archibp_tasks_items`.`items_id` = '$ID'
                      AND `glpi_plugin_archibp_tasks_items`.`itemtype` = '".$item->getType()."' ";

      $query .= getEntitiesRestrictRequest(" AND","glpi_plugin_archibp_tasks",'','',true);

      $query .= " ORDER BY `assocName`";

      $result = $DB->query($query);
      $number = $DB->numrows($result);
      $i      = 0;

      $tasks      = [];
      $task       = new PluginArchibpTask();
      $used          = [];
      if ($numrows = $DB->numrows($result)) {
         while ($data = $DB->fetchAssoc($result)) {
            $tasks[$data['assocID']] = $data;
            $used[$data['id']] = $data['id'];
         }
      }

      if ($canedit && $withtemplate < 2) {
         // Restrict entity for knowbase
         $entities = "";
         $entity   = $_SESSION["glpiactive_entity"];

         if ($item->isEntityAssign()) {
            /// Case of personal items : entity = -1 : create on active entity (Reminder case))
            if ($item->getEntityID() >=0 ) {
               $entity = $item->getEntityID();
            }

            if ($item->isRecursive()) {
               $entities = getSonsOf('glpi_entities',$entity);
            } else {
               $entities = $entity;
            }
         }
         $limit = getEntitiesRestrictRequest(" AND ","glpi_plugin_archibp_tasks",'',$entities,true);
         $q = "SELECT COUNT(*)
               FROM `glpi_plugin_archibp_tasks`
               WHERE `is_deleted` = '0'
               $limit";

         $result = $DB->query($q);
         $nb     = $DB->result($result,0,0);

         echo "<div class='firstbloc'>";


         if (Session::haveRight('plugin_archibp', READ)
             && ($nb > count($used))) {
            echo "<form name='task_form$rand' id='task_form$rand' method='post'
                   action='".Toolbox::getItemTypeFormURL('PluginArchibpTask')."'>";
            echo "<table class='tab_cadre_fixe'>";
            echo "<tr class='tab_bg_1'>";
            echo "<td colspan='4' class='center'>";
            echo "<input type='hidden' name='entities_id' value='$entity'>";
            echo "<input type='hidden' name='is_recursive' value='$is_recursive'>";
            echo "<input type='hidden' name='itemtype' value='".$item->getType()."'>";
            echo "<input type='hidden' name='items_id' value='$ID'>";
            if ($item->getType() == 'Ticket') {
               echo "<input type='hidden' name='tickets_id' value='$ID'>";
            }
            
            PluginArchibpTask::dropdownTask(['entity' => $entities ,
                                                     'used'   => $used]);

            echo "</td><td class='center' width='20%'>";
            echo "<input type='submit' name='additem' value=\"".
                     _sx('button', 'Associate a task', 'archibp')."\" class='submit'>";
            echo "</td>";
            echo "</tr>";
            echo "</table>";
            Html::closeForm();
         }

         echo "</div>";
      }

      echo "<div class='spaced'>";
      if ($canedit && $number && ($withtemplate < 2)) {
         Html::openMassiveActionsForm('mass'.__CLASS__.$rand);
         $massiveactionparams = ['num_displayed'  => $number];
         Html::showMassiveActions($massiveactionparams);
      }
      echo "<table class='tab_cadre_fixe'>";

      echo "<tr>";
      if ($canedit && $number && ($withtemplate < 2)) {
         echo "<th width='10'>".Html::getCheckAllAsCheckbox('mass'.__CLASS__.$rand)."</th>";
      }
      echo "<th>".__('Name')."</th>";
      if (Session::isMultiEntitiesMode()) {
         echo "<th>".__('Entity')."</th>";
      }
      echo "<th>".PluginArchibpTasktype::getTypeName(1)."</th>";
      echo "<th>".PluginArchibpCriticity::getTypeName(1)."</th>";
      echo "<th>".__('Business Process Structure')."</th>";
//      echo "<th>".__('Editor', 'archibp')."</th>";
      echo "</tr>";
      $used = [];

      if ($number) {

         Session::initNavigateListItems('PluginArchibpTask',
                           //TRANS : %1$s is the itemtype name,
                           //        %2$s is the name of the item (used for headings of a list)
                                        sprintf(__('%1$s = %2$s'),
                                                $item->getTypeName(1), $item->getName()));


         foreach  ($tasks as $data) {
            $taskID        = $data["id"];
            $link             = NOT_AVAILABLE;

            if ($task->getFromDB($taskID)) {
               $link         = $task->getLink();
            }

            Session::addToNavigateListItems('PluginArchibpTask', $taskID);

            $used[$taskID]   = $taskID;
            $assocID             = $data["assocID"];

            echo "<tr class='tab_bg_1".($data["is_deleted"]?"_2":"")."'>";
            if ($canedit && ($withtemplate < 2)) {
               echo "<td width='10'>";
               Html::showMassiveActionCheckBox(__CLASS__, $data["assocID"]);
               echo "</td>";
            }
            echo "<td class='center'>$link</td>";
            if (Session::isMultiEntitiesMode()) {
               echo "<td class='center'>".Dropdown::getDropdownName("glpi_entities", $data['entities_id']).
                    "</td>";
            }
            echo "<td>".Dropdown::getDropdownName("glpi_plugin_archibp_tasktypes",$data["plugin_archibp_tasktypes_id"])."</td>";
            echo "<td>".Dropdown::getDropdownName("glpi_plugin_archibp_criticities",$data["plugin_archibp_criticities_id"])."</td>";
      ;
            echo "<td>";
            echo Html::input('completename',['value' => $data['completename'], 'id' => "completename" , 'size' => 30, 'readonly' => true]);
            echo "</td>";
/*            echo "<td>";
            echo "<a href=\"".$CFG_GLPI["root_doc"]."/front/supplier.form.php?id=".$data["suppliers_id"]."\">";
            echo Dropdown::getDropdownName("glpi_suppliers",$data["suppliers_id"]);
            if ($_SESSION["glpiis_ids_visible"] == 1 )
               echo " (".$data["suppliers_id"].")";
            echo "</a></td>";
*/            echo "</tr>";
            $i++;
         }
      }


      echo "</table>";
      if ($canedit && $number && ($withtemplate < 2)) {
         $massiveactionparams['ontop'] = false;
         Html::showMassiveActions($massiveactionparams);
         Html::closeForm();
      }
      echo "</div>";
   }
}

?>
