<?php
/*
 -------------------------------------------------------------------------
 Archibp plugin for GLPI
 Copyright (C) 2020-2022 by Eric Feron.
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
class PluginArchibpConfigbpMenu extends CommonGLPI {
   static $rightname = 'plugin_archibp_configuration';

   static function getMenuName() {
      return _n('Business Process configuration', 'Business Processes configuration', 2, 'archibp');
   }

   static function getMenuContent() {
      global $CFG_GLPI;

		$menu                                           = [];
		$menu['title']                                  = self::getMenuName();
		$menu['page']                                   = "/".Plugin::getWebDir('archibp', false)."/front/configbp.php";
		$menu['links']['search']                        = PluginArchibpConfigbp::getSearchURL(false);
		if (PluginArchibpConfigbp::canCreate()) {
			$menu['links']['add']                        = PluginArchibpConfigbp::getFormURL(false);
		}
		$menu['icon'] = self::getIcon();

		return $menu;
	}

	static function getIcon() {
		return "fas fa-cog";
	}

   static function removeRightsFromSession() {
      if (isset($_SESSION['glpimenu']['configbp']['types']['PluginArchibpConfigbpMenu'])) {
         unset($_SESSION['glpimenu']['configbp']['types']['PluginArchibpConfigbpMenu']); 
      }
      if (isset($_SESSION['glpimenu']['configbp']['content']['pluginarchibpconfigbpmenu'])) {
         unset($_SESSION['glpimenu']['configbp']['content']['pluginarchibpconfigbpmenu']); 
      }
   }
}
