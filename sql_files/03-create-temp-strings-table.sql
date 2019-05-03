CREATE TABLE `dbo_translator`.`game_strings_temp` (
  `pending_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file` int(11) NOT NULL,
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `string` text COLLATE utf8_unicode_ci NOT NULL,
  `status` int(10) unsigned NOT NULL DEFAULT '0',
  `date_added` int(10) unsigned DEFAULT NULL,
  `action_admin` int(10) unsigned DEFAULT NULL,
  `action_date` int(10) unsigned DEFAULT NULL,
  `action_replaced` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`pending_id`),
  KEY `userid` (`user_id`),
  KEY `status` (`status`,`user_id`),
  KEY `strings` (`file`,`id`,`type`,`user_id`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;