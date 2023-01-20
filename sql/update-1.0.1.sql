ALTER TABLE `glpi_plugin_archibp_tasks` 
   ADD COLUMN IF NOT EXISTS `plugin_archibp_taskstates_id` INT(11) NOT NULL default '0' COMMENT 'Active, Future, ...'  AFTER `plugin_archibp_tasktypes_id`,
   ADD KEY IF NOT EXISTS `plugin_archibp_taskstates_id` (`plugin_archibp_taskstates_id`)
;

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
