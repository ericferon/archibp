-- -----------------------------------------------------
-- Table `glpi_plugin_archibp_fieldgroups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `glpi_plugin_archibp_configbpfieldgroups`;
CREATE  TABLE `glpi_plugin_archibp_configbpfieldgroups` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL,
  `comment` VARCHAR(45) NULL,
  `sortorder` TINYINT UNSIGNED NOT NULL,
  `is_visible` TINYINT UNSIGNED NOT NULL COMMENT '0=False/1=True',
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `plugin_archibp_configbpfieldgroups_name` (`name` ASC) )
 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `glpi_plugin_archibp_configbpfieldgroups` (`id` ,`name` ,`comment` ,`sortorder` ,`is_visible`)  VALUES (1,'main','Main characteristics',1,1);
INSERT INTO `glpi_plugin_archibp_configbpfieldgroups` (`id` ,`name` ,`comment` ,`sortorder` ,`is_visible`)  VALUES (2,'other','Other characteristics',3,0);

-- -----------------------------------------------------
-- Table `glpi_plugin_archibp_configbphaligns`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `glpi_plugin_archibp_configbphaligns`;
CREATE  TABLE `glpi_plugin_archibp_configbphaligns` (
  `id` INT(11) UNSIGNED NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `plugin_archibp_configbphaligns_name` (`name`) )
 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `glpi_plugin_archibp_configbphaligns` (`id` ,`name`)  VALUES ('1','Full row');
INSERT INTO `glpi_plugin_archibp_configbphaligns` (`id` ,`name`)  VALUES ('2','Left column');
INSERT INTO `glpi_plugin_archibp_configbphaligns` (`id` ,`name`)  VALUES ('3','Left+Center columns');
INSERT INTO `glpi_plugin_archibp_configbphaligns` (`id` ,`name`)  VALUES ('4','Center column');
INSERT INTO `glpi_plugin_archibp_configbphaligns` (`id` ,`name`)  VALUES ('5','Center+Right columns');
INSERT INTO `glpi_plugin_archibp_configbphaligns` (`id` ,`name`)  VALUES ('6','Right column');

-- -----------------------------------------------------
-- Table `glpi_plugin_archibp_configbpdbfieldtypes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `glpi_plugin_archibp_configbpdbfieldtypes`;
CREATE  TABLE `glpi_plugin_archibp_configbpdbfieldtypes` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL,
  `comment` VARCHAR(255) NULL,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `plugin_archibp_configbpdbfieldtypes_name` (`name` ASC) )
 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `glpi_plugin_archibp_configbpdbfieldtypes` (`id` ,`name` ,`comment`)  VALUES (10,'INT UNSIGNED','Unsigned Integer (range 0 to 4294967295');
INSERT INTO `glpi_plugin_archibp_configbpdbfieldtypes` (`id` ,`name` ,`comment`)  VALUES (11,'TINYINT UNSIGNED','Unsigned Tiny Integer (range 0 to 255)');
INSERT INTO `glpi_plugin_archibp_configbpdbfieldtypes` (`id` ,`name` ,`comment`)  VALUES (12,'SMALLINT UNSIGNED','Unsigned Small Integer (range 0 to 65535)');
INSERT INTO `glpi_plugin_archibp_configbpdbfieldtypes` (`id` ,`name` ,`comment`)  VALUES (20,'INT','Integer (range -2147483648 to 2147483647');
INSERT INTO `glpi_plugin_archibp_configbpdbfieldtypes` (`id` ,`name` ,`comment`)  VALUES (21,'TINYINT','Tiny Integer (range -128 to 127)');
INSERT INTO `glpi_plugin_archibp_configbpdbfieldtypes` (`id` ,`name` ,`comment`)  VALUES (22,'SMALLINT','Small Integer (range -32768 to 32767)');
INSERT INTO `glpi_plugin_archibp_configbpdbfieldtypes` (`id` ,`name` ,`comment`)  VALUES (30,'VARCHAR(255)','Variable character string (max. 255 char.)');
INSERT INTO `glpi_plugin_archibp_configbpdbfieldtypes` (`id` ,`name` ,`comment`)  VALUES (31,'TEXT','Variable character string (max. 65535 char.)');
INSERT INTO `glpi_plugin_archibp_configbpdbfieldtypes` (`id` ,`name` ,`comment`)  VALUES (32,'MEDIUMTEXT','Variable character string (max. 16777215 char.)');
INSERT INTO `glpi_plugin_archibp_configbpdbfieldtypes` (`id` ,`name` ,`comment`)  VALUES (40,'DATETIME','Date and time');
INSERT INTO `glpi_plugin_archibp_configbpdbfieldtypes` (`id` ,`name` ,`comment`)  VALUES (41,'DATE','Date (YYYY-MM-DD)');
INSERT INTO `glpi_plugin_archibp_configbpdbfieldtypes` (`id` ,`name` ,`comment`)  VALUES (42,'TIME','Year (hhh:mm:ss)');
INSERT INTO `glpi_plugin_archibp_configbpdbfieldtypes` (`id` ,`name` ,`comment`)  VALUES (43,'YEAR','Year (YYYY)');

-- -----------------------------------------------------
-- Table `glpi_plugin_archibp_configbpdatatypes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `glpi_plugin_archibp_configbpdatatypes`;
CREATE  TABLE `glpi_plugin_archibp_configbpdatatypes` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL,
  `comment` VARCHAR(255) NULL,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `plugin_archibp_configbpdatatypes_name` (`name` ASC) )
 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `glpi_plugin_archibp_configbpdatatypes` (`id` ,`name` ,`comment`)  VALUES (1,'text','Text');
INSERT INTO `glpi_plugin_archibp_configbpdatatypes` (`id` ,`name` ,`comment`)  VALUES (2,'bool','Boolean');
INSERT INTO `glpi_plugin_archibp_configbpdatatypes` (`id` ,`name` ,`comment`)  VALUES (3,'date','Date');
INSERT INTO `glpi_plugin_archibp_configbpdatatypes` (`id` ,`name` ,`comment`)  VALUES (4,'datetime','Date and time');
INSERT INTO `glpi_plugin_archibp_configbpdatatypes` (`id` ,`name` ,`comment`)  VALUES (5,'number','Key or number');
INSERT INTO `glpi_plugin_archibp_configbpdatatypes` (`id` ,`name` ,`comment`)  VALUES (6,'dropdown','Dropdown');
INSERT INTO `glpi_plugin_archibp_configbpdatatypes` (`id` ,`name` ,`comment`)  VALUES (7,'itemlink','Itemlink');
INSERT INTO `glpi_plugin_archibp_configbpdatatypes` (`id` ,`name` ,`comment`)  VALUES (8,'textarea','Text editor');

-- -----------------------------------------------------
-- Table `glpi_plugin_archibp_configbplinks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `glpi_plugin_archibp_configbplinks`;
CREATE  TABLE `glpi_plugin_archibp_configbplinks` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL,
  `has_dropdown` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '0=False/1=True',
  `is_entity_limited` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '0=False/1=True',
  `is_tree_dropdown` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '0=False/1=True',
  `as_view_on` VARCHAR(255) NULL COMMENT 'empty or table name',
  `viewlimit` VARCHAR(255) NULL COMMENT 'empty or where clause (without where reserved word)',
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `plugin_archibp_configbplinks_name` (`name` ASC) )
 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `glpi_plugin_archibp_configbplinks` (`id`,`name`,`has_dropdown`,`is_entity_limited`) VALUES (1,'PluginArchiswSwcomponent',0,1);
INSERT INTO `glpi_plugin_archibp_configbplinks` (`id`,`name`,`has_dropdown`,`is_entity_limited`) VALUES (2,'PluginArchidataDataelement',0,1);
INSERT INTO `glpi_plugin_archibp_configbplinks` (`id`,`name`,`has_dropdown`,`is_entity_limited`) VALUES (3,'PluginArchibpTask',0,1);
INSERT INTO `glpi_plugin_archibp_configbplinks` (`id`,`name`,`has_dropdown`,`is_entity_limited`) VALUES (4,'PluginArchifunFuncarea',0,1);
INSERT INTO `glpi_plugin_archibp_configbplinks` (`id`,`name`,`has_dropdown`,`is_entity_limited`) VALUES (5,'Computer',0,1);
INSERT INTO `glpi_plugin_archibp_configbplinks` (`id`,`name`,`has_dropdown`,`is_entity_limited`) VALUES (6,'Software',0,1);
INSERT INTO `glpi_plugin_archibp_configbplinks` (`id`,`name`,`has_dropdown`,`is_entity_limited`) VALUES (7,'Appliance',0,1);
INSERT INTO `glpi_plugin_archibp_configbplinks` (`id`,`name`,`has_dropdown`,`is_entity_limited`) VALUES (8,'Contract',0,1);
INSERT INTO `glpi_plugin_archibp_configbplinks` (`id`,`name`,`has_dropdown`,`is_entity_limited`) VALUES (9,'Entity',0,0);
INSERT INTO `glpi_plugin_archibp_configbplinks` (`id`,`name`,`has_dropdown`,`is_entity_limited`) VALUES (10,'Project',0,1);
INSERT INTO `glpi_plugin_archibp_configbplinks` (`id`,`name`,`has_dropdown`,`is_entity_limited`) VALUES (11,'ProjectTask',0,1);
INSERT INTO `glpi_plugin_archibp_configbplinks` (`id`,`name`,`has_dropdown`,`is_entity_limited`) VALUES (12,'User',0,0);
INSERT INTO `glpi_plugin_archibp_configbplinks` (`id`,`name`,`has_dropdown`,`is_entity_limited`) VALUES (13,'Group',0,1);
INSERT INTO `glpi_plugin_archibp_configbplinks` (`id`,`name`,`has_dropdown`,`is_entity_limited`) VALUES (14,'Location',0,1);
INSERT INTO `glpi_plugin_archibp_configbplinks` (`id`,`name`,`has_dropdown`,`is_entity_limited`) VALUES (15,'Supplier',0,1);
INSERT INTO `glpi_plugin_archibp_configbplinks` (`id`,`name`,`has_dropdown`,`is_entity_limited`) VALUES (16,'Manufacturer',0,0);
INSERT INTO `glpi_plugin_archibp_configbplinks` (`id`,`name`,`has_dropdown`,`is_entity_limited`) VALUES (17,'PluginArchibpTaskstate',0,0);
INSERT INTO `glpi_plugin_archibp_configbplinks` (`id`,`name`,`has_dropdown`,`is_entity_limited`) VALUES (18,'PluginArchibpCriticity',0,0);
INSERT INTO `glpi_plugin_archibp_configbplinks` (`id`,`name`,`has_dropdown`,`is_entity_limited`) VALUES (19,'PluginArchibpTasktype',0,0);
INSERT INTO `glpi_plugin_archibp_configbplinks` (`id`,`name`,`has_dropdown`,`is_entity_limited`) VALUES (20,'PluginArchibpTaskTarget',0,0);
INSERT INTO `glpi_plugin_archibp_configbplinks` (`id`,`name`,`has_dropdown`,`is_entity_limited`,`is_tree_dropdown`,`as_view_on`,`viewlimit`) VALUES (21,'PluginArchibpSwcomponent',0,1,1,'glpi_plugin_archisw_swcomponents', 'level = ''1'' ');

-- -----------------------------------------------------
-- Table `glpi_plugin_archibp_configbps`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `glpi_plugin_archibp_configbps`;
CREATE  TABLE `glpi_plugin_archibp_configbps` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `plugin_archibp_configbpfieldgroups_id` INT(11) UNSIGNED NOT NULL,
  `row` TINYINT UNSIGNED NOT NULL,
  `plugin_archibp_configbphaligns_id` INT(11) UNSIGNED NOT NULL COMMENT 'Left/Center/Right column or Full line',
  `plugin_archibp_configbpdbfieldtypes_id` INT(11) UNSIGNED NOT NULL,
  `plugin_archibp_configbpdatatypes_id` INT(11) UNSIGNED NOT NULL,
  `nosearch` CHAR(1) NOT NULL COMMENT '0=False/1=True',
  `massiveaction` CHAR(1) NOT NULL COMMENT '0=False/1=True',
  `forcegroupby` CHAR(1) NOT NULL COMMENT '0=False/1=True',
  `is_linked` TINYINT UNSIGNED NOT NULL COMMENT '0=False/1=True',
  `plugin_archibp_configbplinks_id` INT(11) UNSIGNED,
  `linkfield` VARCHAR(255),
  `joinparams` VARCHAR(255),
  `description` VARCHAR(45) NOT NULL,
  `is_readonly` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '0=False/1=True',
  `is_deleted` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '0=False/1=True',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unicity` (`plugin_archibp_configbpfieldgroups_id`, `row`, `plugin_archibp_configbphaligns_id`),
  UNIQUE INDEX `plugin_archibp_configbps_name` (`name` ASC) )
 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `glpi_plugin_archibp_configbps` (`id`,`name`,`plugin_archibp_configbpfieldgroups_id`,`row`,`plugin_archibp_configbphaligns_id`,`plugin_archibp_configbpdbfieldtypes_id`,`plugin_archibp_configbpdatatypes_id`,`nosearch`,`massiveaction`,`forcegroupby`,`is_linked`,`plugin_archibp_configbplinks_id`,`linkfield`,`joinparams`,`is_readonly`,`description`,`is_deleted`) VALUES (null,'level',0,2,6,20,1,1,0,0,0,0,'','',1,'Level',0);
INSERT INTO `glpi_plugin_archibp_configbps` (`id`,`name`,`plugin_archibp_configbpfieldgroups_id`,`row`,`plugin_archibp_configbphaligns_id`,`plugin_archibp_configbpdbfieldtypes_id`,`plugin_archibp_configbpdatatypes_id`,`nosearch`,`massiveaction`,`forcegroupby`,`is_linked`,`plugin_archibp_configbplinks_id`,`linkfield`,`joinparams`,`is_readonly`,`description`,`is_deleted`) VALUES (null,'description',0,3,1,32,8,1,0,0,0,0,'','',0,'Description',0);
INSERT INTO `glpi_plugin_archibp_configbps` (`id`,`name`,`plugin_archibp_configbpfieldgroups_id`,`row`,`plugin_archibp_configbphaligns_id`,`plugin_archibp_configbpdbfieldtypes_id`,`plugin_archibp_configbpdatatypes_id`,`nosearch`,`massiveaction`,`forcegroupby`,`is_linked`,`plugin_archibp_configbplinks_id`,`linkfield`,`joinparams`,`is_readonly`,`description`,`is_deleted`) VALUES (null,'plugin_archibp_tasktypes_id',1,2,2,10,6,1,0,0,1,19,'','',0,'Type',0);
INSERT INTO `glpi_plugin_archibp_configbps` (`id`,`name`,`plugin_archibp_configbpfieldgroups_id`,`row`,`plugin_archibp_configbphaligns_id`,`plugin_archibp_configbpdbfieldtypes_id`,`plugin_archibp_configbpdatatypes_id`,`nosearch`,`massiveaction`,`forcegroupby`,`is_linked`,`plugin_archibp_configbplinks_id`,`linkfield`,`joinparams`,`is_readonly`,`description`,`is_deleted`) VALUES (null,'plugin_archibp_criticities_id',1,1,6,10,6,1,0,0,1,18,'','',0,'Criticity',0);
INSERT INTO `glpi_plugin_archibp_configbps` (`id`,`name`,`plugin_archibp_configbpfieldgroups_id`,`row`,`plugin_archibp_configbphaligns_id`,`plugin_archibp_configbpdbfieldtypes_id`,`plugin_archibp_configbpdatatypes_id`,`nosearch`,`massiveaction`,`forcegroupby`,`is_linked`,`plugin_archibp_configbplinks_id`,`linkfield`,`joinparams`,`is_readonly`,`description`,`is_deleted`) VALUES (null,'plugin_archibp_tasktargets_id',1,2,6,10,6,1,0,0,1,20,'','',0,'Targets',0);
INSERT INTO `glpi_plugin_archibp_configbps` (`id`,`name`,`plugin_archibp_configbpfieldgroups_id`,`row`,`plugin_archibp_configbphaligns_id`,`plugin_archibp_configbpdbfieldtypes_id`,`plugin_archibp_configbpdatatypes_id`,`nosearch`,`massiveaction`,`forcegroupby`,`is_linked`,`plugin_archibp_configbplinks_id`,`linkfield`,`joinparams`,`is_readonly`,`description`,`is_deleted`) VALUES (null,'plugin_archibp_taskstates_id',1,1,2,10,6,1,1,0,1,17,'','',0,'Status',0);
INSERT INTO `glpi_plugin_archibp_configbps` (`id`,`name`,`plugin_archibp_configbpfieldgroups_id`,`row`,`plugin_archibp_configbphaligns_id`,`plugin_archibp_configbpdbfieldtypes_id`,`plugin_archibp_configbpdatatypes_id`,`nosearch`,`massiveaction`,`forcegroupby`,`is_linked`,`plugin_archibp_configbplinks_id`,`linkfield`,`joinparams`,`is_readonly`,`description`,`is_deleted`) VALUES (null,'plugin_archibp_swcomponents_id',1,3,2,10,6,1,0,0,1,21,'','',0,'Linked to application',0);
INSERT INTO `glpi_plugin_archibp_configbps` (`id`,`name`,`plugin_archibp_configbpfieldgroups_id`,`row`,`plugin_archibp_configbphaligns_id`,`plugin_archibp_configbpdbfieldtypes_id`,`plugin_archibp_configbpdatatypes_id`,`nosearch`,`massiveaction`,`forcegroupby`,`is_linked`,`plugin_archibp_configbplinks_id`,`linkfield`,`joinparams`,`is_readonly`,`description`,`is_deleted`) VALUES (null,'transactioncode',1,3,6,30,1,1,0,0,0,0,'','',0,'Transaction code',0);
INSERT INTO `glpi_plugin_archibp_configbps` (`id`,`name`,`plugin_archibp_configbpfieldgroups_id`,`row`,`plugin_archibp_configbphaligns_id`,`plugin_archibp_configbpdbfieldtypes_id`,`plugin_archibp_configbpdatatypes_id`,`nosearch`,`massiveaction`,`forcegroupby`,`is_linked`,`plugin_archibp_configbplinks_id`,`linkfield`,`joinparams`,`is_readonly`,`description`,`is_deleted`) VALUES (null,'groups_id',1,4,2,10,6,1,0,0,1,13,'groups_id','',0,'Task Owner''s group',0);
INSERT INTO `glpi_plugin_archibp_configbps` (`id`,`name`,`plugin_archibp_configbpfieldgroups_id`,`row`,`plugin_archibp_configbphaligns_id`,`plugin_archibp_configbpdbfieldtypes_id`,`plugin_archibp_configbpdatatypes_id`,`nosearch`,`massiveaction`,`forcegroupby`,`is_linked`,`plugin_archibp_configbplinks_id`,`linkfield`,`joinparams`,`is_readonly`,`description`,`is_deleted`) VALUES (null,'users_id',1,4,6,10,6,1,0,0,1,12,'users_id','',0,'Task Expert',0);
INSERT INTO `glpi_plugin_archibp_configbps` (`id`,`name`,`plugin_archibp_configbpfieldgroups_id`,`row`,`plugin_archibp_configbphaligns_id`,`plugin_archibp_configbpdbfieldtypes_id`,`plugin_archibp_configbpdatatypes_id`,`nosearch`,`massiveaction`,`forcegroupby`,`is_linked`,`plugin_archibp_configbplinks_id`,`linkfield`,`joinparams`,`is_readonly`,`description`,`is_deleted`) VALUES (null,'address',2,2,1,30,7,1,0,0,0,0,'','',0,'URL doc.',0);
INSERT INTO `glpi_plugin_archibp_configbps` (`id`,`name`,`plugin_archibp_configbpfieldgroups_id`,`row`,`plugin_archibp_configbphaligns_id`,`plugin_archibp_configbpdbfieldtypes_id`,`plugin_archibp_configbpdatatypes_id`,`nosearch`,`massiveaction`,`forcegroupby`,`is_linked`,`plugin_archibp_configbplinks_id`,`linkfield`,`joinparams`,`is_readonly`,`description`,`is_deleted`) VALUES (null,'comment',2,1,1,32,8,0,0,0,0,0,'','',0,'Comment',0);

-- ----------------------------------
-- Statecheck rules
-- ----------------------------------
INSERT INTO `glpi_plugin_statecheck_tables` (`id`,`name`,`comment`,`statetable`,`stateclass`,`class`,`frontname`) VALUES (null,'glpi_plugin_archibp_configbps', 'Business Process configuration', 'glpi_plugin_archibp_configbpdatatypes', 'PluginArchibpConfigbpDatatype', 'PluginArchibpConfigbp', 'configbp');
SELECT @table_id := LAST_INSERT_ID();
INSERT INTO `glpi_plugin_statecheck_rules` (`id`,`entities_id`,`name`,`plugin_statecheck_tables_id`,`plugin_statecheck_targetstates_id`,`ranking`,`match`,`is_active`,`comment`,`successnotifications_id`,`failurenotifications_id`,`date_mod`,`is_recursive`) VALUES (null,0,'Business Process configuration - reserved words',@table_id,0,1,'AND',true,'Do not delete',0,0,NOW(),true);
SELECT @rule_id := LAST_INSERT_ID();
INSERT INTO `glpi_plugin_statecheck_ruleactions` (`id`,`plugin_statecheck_rules_id`,`action_type`,`field`,`value`) VALUES (null,@rule_id,'isnot','name','name');
INSERT INTO `glpi_plugin_statecheck_ruleactions` (`id`,`plugin_statecheck_rules_id`,`action_type`,`field`,`value`) VALUES (null,@rule_id,'isnot','name','completename');
INSERT INTO `glpi_plugin_statecheck_ruleactions` (`id`,`plugin_statecheck_rules_id`,`action_type`,`field`,`value`) VALUES (null,@rule_id,'isnot','name','is_deleted');
INSERT INTO `glpi_plugin_statecheck_ruleactions` (`id`,`plugin_statecheck_rules_id`,`action_type`,`field`,`value`) VALUES (null,@rule_id,'isnot','name','entities_id');
INSERT INTO `glpi_plugin_statecheck_ruleactions` (`id`,`plugin_statecheck_rules_id`,`action_type`,`field`,`value`) VALUES (null,@rule_id,'isnot','name','id');
INSERT INTO `glpi_plugin_statecheck_ruleactions` (`id`,`plugin_statecheck_rules_id`,`action_type`,`field`,`value`) VALUES (null,@rule_id,'isnot','name','is_recursive');
INSERT INTO `glpi_plugin_statecheck_ruleactions` (`id`,`plugin_statecheck_rules_id`,`action_type`,`field`,`value`) VALUES (null,@rule_id,'isnot','name','sons_cache');
INSERT INTO `glpi_plugin_statecheck_ruleactions` (`id`,`plugin_statecheck_rules_id`,`action_type`,`field`,`value`) VALUES (null,@rule_id,'isnot','name','ancestors_cache');
INSERT INTO `glpi_plugin_statecheck_ruleactions` (`id`,`plugin_statecheck_rules_id`,`action_type`,`field`,`value`) VALUES (null,@rule_id,'isnot','name','date_mod');
INSERT INTO `glpi_plugin_statecheck_rules` (`id`,`entities_id`,`name`,`plugin_statecheck_tables_id`,`plugin_statecheck_targetstates_id`,`ranking`,`match`,`is_active`,`comment`,`successnotifications_id`,`failurenotifications_id`,`date_mod`,`is_recursive`) VALUES (null,0,'Business Process configuration - mandatory fields',@table_id,0,1,'AND',true,'Do not delete',0,0,NOW(),true);
SELECT @rule_id := LAST_INSERT_ID();
INSERT INTO `glpi_plugin_statecheck_ruleactions` (`id`,`plugin_statecheck_rules_id`,`action_type`,`field`,`value`) VALUES (null,@rule_id,'isnotempty','row',null);
INSERT INTO `glpi_plugin_statecheck_ruleactions` (`id`,`plugin_statecheck_rules_id`,`action_type`,`field`,`value`) VALUES (null,@rule_id,'isnotempty','plugin_archibp_configbphaligns_id',null);
INSERT INTO `glpi_plugin_statecheck_ruleactions` (`id`,`plugin_statecheck_rules_id`,`action_type`,`field`,`value`) VALUES (null,@rule_id,'isnotempty','description',null);
INSERT INTO `glpi_plugin_statecheck_ruleactions` (`id`,`plugin_statecheck_rules_id`,`action_type`,`field`,`value`) VALUES (null,@rule_id,'isnotempty','plugin_archibp_configbpdbfieldtypes_id',null);
INSERT INTO `glpi_plugin_statecheck_ruleactions` (`id`,`plugin_statecheck_rules_id`,`action_type`,`field`,`value`) VALUES (null,@rule_id,'isnotempty','plugin_archibp_configbpdatatypes_id',null);
INSERT INTO `glpi_plugin_statecheck_rules` (`id`,`entities_id`,`name`,`plugin_statecheck_tables_id`,`plugin_statecheck_targetstates_id`,`ranking`,`match`,`is_active`,`comment`,`successnotifications_id`,`failurenotifications_id`,`date_mod`,`is_recursive`) VALUES (null,0,'Business Process configuration - not dropdown',@table_id,0,1,'AND',true,'Do not delete
/nIf the field is not a dropdown,
/n- a name must be lowercase, start with a letter, contain only letters, numbers or underscores
/n- a name may not end with "s_id" ((?&#60;!a) is a negated lookbehind assertion that ensures, that before the end of the string (or row with m modifier), there is not the character "a")',0,0,NOW(),true);
SELECT @rule_id := LAST_INSERT_ID();
INSERT INTO `glpi_plugin_statecheck_ruleactions` (`id`,`plugin_statecheck_rules_id`,`action_type`,`field`,`value`) VALUES (null,@rule_id,'regex_check','name','/^[a-z][a-z0-9_]*$/');
INSERT INTO `glpi_plugin_statecheck_ruleactions` (`id`,`plugin_statecheck_rules_id`,`action_type`,`field`,`value`) VALUES (null,@rule_id,'regex_check','name','/.*(?&#60;!s_id)$/m');
INSERT INTO `glpi_plugin_statecheck_rulecriterias` (`id`,`plugin_statecheck_rules_id`,`criteria`,`condition`,`pattern`) VALUES (null,@rule_id,'plugin_archibp_configbpdatatypes_id',1,6);
INSERT INTO `glpi_plugin_statecheck_rules` (`id`,`entities_id`,`name`,`plugin_statecheck_tables_id`,`plugin_statecheck_targetstates_id`,`ranking`,`match`,`is_active`,`comment`,`successnotifications_id`,`failurenotifications_id`,`date_mod`,`is_recursive`) VALUES (null,0,'Business Process configuration - dropdown',@table_id,6,1,'AND',true,'Do not delete',0,0,NOW(),true);
SELECT @rule_id := LAST_INSERT_ID();
INSERT INTO `glpi_plugin_statecheck_ruleactions` (`id`,`plugin_statecheck_rules_id`,`action_type`,`field`,`value`) VALUES (null,@rule_id,'regex_check','name','/^[a-z][a-z0-9_]*s_id$/');
INSERT INTO `glpi_plugin_statecheck_ruleactions` (`id`,`plugin_statecheck_rules_id`,`action_type`,`field`,`value`) VALUES (null,@rule_id,'is','is_linked','1');
INSERT INTO `glpi_plugin_statecheck_ruleactions` (`id`,`plugin_statecheck_rules_id`,`action_type`,`field`,`value`) VALUES (null,@rule_id,'isnotempty','plugin_archibp_configbplinks_id',null);
INSERT INTO `glpi_plugin_statecheck_ruleactions` (`id`,`plugin_statecheck_rules_id`,`action_type`,`field`,`value`) VALUES (null,@rule_id,'is','plugin_archibp_configbpdbfieldtypes_id',10);

INSERT INTO `glpi_plugin_statecheck_tables` (`id`,`name`,`comment`,`statetable`,`stateclass`,`class`,`frontname`) VALUES (null,'glpi_plugin_archibp_configbplinks', 'Business Process configuration links', '', '', 'PluginArchibpConfigbpLink', 'configbplink');
SELECT @table_id := LAST_INSERT_ID();
INSERT INTO `glpi_plugin_statecheck_rules` (`id`,`entities_id`,`name`,`plugin_statecheck_tables_id`,`plugin_statecheck_targetstates_id`,`ranking`,`match`,`is_active`,`comment`,`successnotifications_id`,`failurenotifications_id`,`date_mod`,`is_recursive`) VALUES (null,0,'Business Process configuration links for dropdown',@table_id,0,1,'AND',true,'Do not delete : set temporarily inactive, if needed',0,0,NOW(),true);
SELECT @rule_id := LAST_INSERT_ID();
INSERT INTO `glpi_plugin_statecheck_ruleactions` (`id`,`plugin_statecheck_rules_id`,`action_type`,`field`,`value`) VALUES (null,@rule_id,'regex_check','name','/^PluginArchibp[a-zA-Z0-9]+$/');

INSERT INTO `glpi_plugin_statecheck_tables` (`id`,`name`,`comment`,`statetable`,`stateclass`,`class`,`frontname`) VALUES (null,'glpi_plugin_archibp_configbpfieldgroups', 'Business Process field groups', '', '', 'PluginArchibpConfigbpFieldgroup', 'configbpfieldgroup');
SELECT @table_id := LAST_INSERT_ID();
INSERT INTO `glpi_plugin_statecheck_rules` (`id`,`entities_id`,`name`,`plugin_statecheck_tables_id`,`plugin_statecheck_targetstates_id`,`ranking`,`match`,`is_active`,`comment`,`successnotifications_id`,`failurenotifications_id`,`date_mod`,`is_recursive`) VALUES (null,0,'Business Process Field Groups - mandatory fields',@table_id,0,1,'AND',true,'Do not delete',0,0,NOW(),true);
SELECT @rule_id := LAST_INSERT_ID();
INSERT INTO `glpi_plugin_statecheck_ruleactions` (`id`,`plugin_statecheck_rules_id`,`action_type`,`field`,`value`) VALUES (null,@rule_id,'isnotempty','name',null);
INSERT INTO `glpi_plugin_statecheck_ruleactions` (`id`,`plugin_statecheck_rules_id`,`action_type`,`field`,`value`) VALUES (null,@rule_id,'isnotempty','comment',null);
INSERT INTO `glpi_plugin_statecheck_ruleactions` (`id`,`plugin_statecheck_rules_id`,`action_type`,`field`,`value`) VALUES (null,@rule_id,'isnotempty','sortorder',null);

INSERT INTO `glpi_displaypreferences` VALUES (NULL,'PluginArchibpConfigbp',2,1,0);
INSERT INTO `glpi_displaypreferences` VALUES (NULL,'PluginArchibpConfigbp',3,2,0);
INSERT INTO `glpi_displaypreferences` VALUES (NULL,'PluginArchibpConfigbp',11,3,0);
INSERT INTO `glpi_displaypreferences` VALUES (NULL,'PluginArchibpConfigbp',12,4,0);
INSERT INTO `glpi_displaypreferences` VALUES (NULL,'PluginArchibpConfigbp',4,5,0);
INSERT INTO `glpi_displaypreferences` VALUES (NULL,'PluginArchibpConfigbp',10,6,0);
