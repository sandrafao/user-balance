USE `user_balance` ;

CREATE TABLE IF NOT EXISTS `user_balance` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_identifier` INT(11) NOT NULL UNIQUE,
  `balance` DECIMAL(13, 4) UNSIGNED NOT NULL,
  `last_modified` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_identifier`)
) CHARACTER SET utf8 COLLATE utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `transactions` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_identifier` INT(11) NOT NULL,
  `ammount` DECIMAL(13, 4) UNSIGNED NOT NULL,
  `operation_type` CHAR(10) NOT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_operation` (`user_identifier`, `operation_type`)
) CHARACTER SET utf8 COLLATE utf8_unicode_ci;