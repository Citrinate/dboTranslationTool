CREATE TABLE `dbo_translator`.`game_strings` (
  `file` int(11) NOT NULL,
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `kor` text COLLATE utf8_unicode_ci,
  `tw` text COLLATE utf8_unicode_ci,
  `hk` text COLLATE utf8_unicode_ci,
  `en` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`file`,`id`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;