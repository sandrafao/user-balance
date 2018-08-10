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
  `identifier` CHAR(100) NOT NULL UNIQUE,
  `user_to` INT(11) DEFAULT NULL,
  `user_from` INT(11) DEFAULT NULL,
  `ammount` DECIMAL(13, 4) UNSIGNED NOT NULL,
  `operation_type` CHAR(10) NOT NULL,
  `status` CHAR(50) NOT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) CHARACTER SET utf8 COLLATE utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `retry_counter` (
  `transaction_id` CHAR(100) NOT NULL,
  `retries` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`transaction_id`)
);