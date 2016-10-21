-- ecshop v2.x SQL Dump Program
-- http://openskyphone.com
-- 
-- DATE : 2016-09-28 01:09:51
-- MYSQL SERVER VERSION : 5.5.5-10.1.13-MariaDB
-- PHP VERSION : 5.6.23
-- ECShop VERSION : v3.0.0
-- Vol : 1
DROP TABLE IF EXISTS `ecs_remote`;
CREATE TABLE `ecs_remote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `city` varchar(128) DEFAULT NULL,
  `country` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- END ecshop v2.x SQL Dump Program 