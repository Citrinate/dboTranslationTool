CREATE TABLE IF NOT EXISTS `dbo_translator_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing user_id of each user, unique index',
  `user_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s name, unique',
  `user_password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s password in salted and hashed format',
  `hasAccess` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'whether or not the user has permission to submit translations',
  `isAdmin` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'whether or not the user is an admin'
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='user data';