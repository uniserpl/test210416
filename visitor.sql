-- Adminer 4.7.6 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `visitor`;
CREATE TABLE `visitor` (
  `ip_address` int(11) unsigned NOT NULL COMMENT 'IP адрес',
  `user_agent` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'UA',
  `view_date` int(11) NOT NULL COMMENT 'UNIXTIME',
  `page_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'URL',
  `views_count` int(11) DEFAULT '0' COMMENT 'Число просмотров',
  PRIMARY KEY (`ip_address`,`user_agent`,`page_url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Посетители';


-- 2021-04-16 14:13:07
