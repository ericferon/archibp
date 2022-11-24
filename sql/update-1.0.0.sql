ALTER TABLE `glpi_plugin_archibp_tasks` 
   ADD COLUMN IF NOT EXISTS `plugin_archibp_tasktargets_id` INT(11) NOT NULL default '0' COMMENT 'Target user segments (department A, B, A+B, ...)' AFTER `plugin_archibp_swcomponents_id`,
   ADD KEY IF NOT EXISTS `plugin_archibp_tasktargets_id` (`plugin_archibp_tasktargets_id`)
;

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
