
-- -----------------------------------------------------
-- Table `glpi_plugin_archibp_tasks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `glpi_plugin_archibp_tasks`;
CREATE TABLE `glpi_plugin_archibp_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entities_id` int(11) NOT NULL DEFAULT '0',
  `is_recursive` tinyint(1) NOT NULL default '0',
  `plugin_archibp_tasks_id` int(11) NOT NULL DEFAULT '0',
  `completename` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `level` int(11) NOT NULL DEFAULT '0',
  `sons_cache` longtext COLLATE utf8mb4_unicode_ci,
  `ancestors_cache` longtext COLLATE utf8mb4_unicode_ci,
  `plugin_archibp_tasktypes_id` INT(11) NOT NULL default '0' COMMENT 'BP type : Manual, ...' ,
  `plugin_archibp_taskstates_id` INT(11) NOT NULL default '0' COMMENT 'Active, Future, ...' ,
  `plugin_archibp_criticities_id` INT(11) NOT NULL default '0' COMMENT 'BP criticality : Normal, Important, ...' ,
  `plugin_archibp_swcomponents_id` INT(11) NOT NULL default '0' COMMENT 'from archisw',
  `plugin_archibp_tasktargets_id` INT(11) NOT NULL default '0' COMMENT 'Target user segments (department A, B, A+B, ...)' ,
  `transactioncode` text COLLATE utf8mb4_unicode_ci,
  `address` varchar(255) collate utf8mb4_unicode_ci default NULL,
  `users_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_users (id)',
  `groups_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_groups (id)',
  `date_mod` datetime default NULL,
  `is_helpdesk_visible` int(11) NOT NULL default '1',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unicity` (`plugin_archibp_tasks_id`,`name`),
  KEY `plugin_archibp_tasktypes_id` (`plugin_archibp_tasktypes_id`),
  KEY `plugin_archibp_taskstates_id` (`plugin_archibp_taskstates_id`),
  KEY `plugin_archibp_criticities_id` (`plugin_archibp_criticities_id`),
  KEY `plugin_archibp_swcomponents_id` (`plugin_archibp_swcomponents_id`),
  KEY `plugin_archibp_tasktargets_id` (`plugin_archibp_tasktargets_id`),
  KEY `users_id` (`users_id`),
  KEY `groups_id` (`groups_id`),
  KEY date_mod (date_mod),
  KEY is_helpdesk_visible (is_helpdesk_visible),
  KEY `is_deleted` (`is_deleted`),
  KEY `plugin_archibp_tasks_id` (`plugin_archibp_tasks_id`)
) AUTO_INCREMENT=756 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ;

-- ----------------------------------------------------------------
-- Table `glpi_plugin_archibp_tasks_items`
-- ----------------------------------------------------------------
DROP TABLE IF EXISTS `glpi_plugin_archibp_tasks_items`;
CREATE TABLE `glpi_plugin_archibp_tasks_items` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`plugin_archibp_tasks_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugin_archibp_tasks (id)',
	`items_id` int(11) NOT NULL default '0' COMMENT 'RELATION to various tables, according to itemtype (id)',
   `itemtype` varchar(100) collate utf8mb4_unicode_ci NOT NULL COMMENT 'see .class.php file',
	PRIMARY KEY  (`id`),
	UNIQUE KEY `unicity` (`plugin_archibp_tasks_id`,`items_id`,`itemtype`),
  KEY `FK_device` (`items_id`,`itemtype`),
  KEY `item` (`itemtype`,`items_id`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `glpi_plugin_archibp_profiles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `glpi_plugin_archibp_profiles`;
CREATE TABLE `glpi_plugin_archibp_profiles` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`profiles_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_profiles (id)',
	`archibp` char(1) collate utf8mb4_unicode_ci default NULL,
	`open_ticket` char(1) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY  (`id`),
	KEY `profiles_id` (`profiles_id`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `glpi_plugin_archibp_tasktypes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `glpi_plugin_archibp_tasktypes`;
CREATE  TABLE `glpi_plugin_archibp_tasktypes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `comment` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `glpi_plugin_archibp_tasktypes_name` (`name` ASC) 
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `glpi_plugin_archibp_tasktypes` ( `id` , `name` , `comment` )  VALUES (1,'Manual','Manual');
INSERT INTO `glpi_plugin_archibp_tasktypes` ( `id` , `name` , `comment` )  VALUES (2,'App UI','Application User Interface');
INSERT INTO `glpi_plugin_archibp_tasktypes` ( `id` , `name` , `comment` )  VALUES (3,'App Background','Application Background Processing');

-- -----------------------------------------------------
-- Table `plugin_archibp_criticities`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `glpi_plugin_archibp_criticities`;
CREATE  TABLE `glpi_plugin_archibp_criticities` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `comment` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `glpi_plugin_archibp_criticities_name` (`name` ASC) 
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- View `glpi_plugin_archibp_swcomponents`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `glpi_plugin_archibp_swcomponents`;
CREATE OR REPLACE VIEW `glpi_plugin_archibp_swcomponents` (`id`, `entities_id`, `name`, `comment`) AS
SELECT `id`, `entities_id`, `completename`, `comment` from `glpi_plugin_archisw_swcomponents` where level = '1';

INSERT INTO `glpi_displaypreferences` VALUES (NULL,'PluginArchibpTask','2','2','0');
INSERT INTO `glpi_displaypreferences` VALUES (NULL,'PluginArchibpTask','6','3','0');
INSERT INTO `glpi_displaypreferences` VALUES (NULL,'PluginArchibpTask','7','4','0');
	
-- -----------------------------------------------------
-- Table `plugin_archibp_tasktargets`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `glpi_plugin_archibp_tasktargets`;
CREATE TABLE `glpi_plugin_archibp_tasktargets` (
	`id` int(11) NOT NULL auto_increment,
	`name` varchar(255) collate utf8mb4_unicode_ci default NULL,
   `comment` text collate utf8mb4_unicode_ci,
	PRIMARY KEY  (`id`),
	KEY `name` (`name`)
)  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `glpi_plugin_archibp_taskstates`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `glpi_plugin_archibp_taskstates`;
CREATE  TABLE `glpi_plugin_archibp_taskstates` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `comment` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `glpi_plugin_archibp_taskstates_name` (`name` ASC) 
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
