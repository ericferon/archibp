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

include ('../../../inc/includes.php');

if (!isset($_GET["id"])) $_GET["id"] = "";
if (!isset($_GET["withtemplate"])) $_GET["withtemplate"] = "";

$task=new PluginArchibpTask();
$task_item=new PluginArchibpTask_Item();

if (isset($_POST["add"])) {

   $task->check(-1, CREATE,$_POST);
   $newID=$task->add($_POST);
   if ($_SESSION['glpibackcreated']) {
      Html::redirect($task->getFormURL()."?id=".$newID);
   }
   Html::back();

} else if (isset($_POST["delete"])) {

   $task->check($_POST['id'], DELETE);
   $task->delete($_POST);
   $task->redirectToList();

} else if (isset($_POST["restore"])) {

   $task->check($_POST['id'], PURGE);
   $task->restore($_POST);
   $task->redirectToList();

} else if (isset($_POST["purge"])) {

   $task->check($_POST['id'], PURGE);
   $task->delete($_POST,1);
   $task->redirectToList();

} else if (isset($_POST["update"])) {

   $task->check($_POST['id'], UPDATE);
   $task->update($_POST);
   Html::back();

} else if (isset($_POST["additem"])) {

   if (!empty($_POST['itemtype'])&&$_POST['items_id']>0) {
      $task_item->check(-1, UPDATE, $_POST);
      $task_item->addItem($_POST);
   }
   Html::back();

} else if (isset($_POST["deleteitem"])) {

   foreach ($_POST["item"] as $key => $val) {
         $input = ['id' => $key];
         if ($val==1) {
            $task_item->check($key, UPDATE);
            $task_item->delete($input);
         }
      }
   Html::back();

} else if (isset($_POST["deletearchibp"])) {

   $input = ['id' => $_POST["id"]];
   $task_item->check($_POST["id"], UPDATE);
   $task_item->delete($input);
   Html::back();

} else {

   $task->checkGlobal(READ);

   $plugin = new Plugin();
   Html::header(PluginArchibpTask::getTypeName(2), '', "assets",
                   "pluginarchibpmenu");
   $task->display($_GET);

   Html::footer();
}

?>
