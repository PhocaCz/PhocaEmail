CREATE TABLE IF NOT EXISTS `#__phocaemail_lists` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(250) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `description` text,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `access` int(11) unsigned NOT NULL default '0',
  `params` text,
  `language` char(7) NOT NULL default '',
  PRIMARY KEY  (`id`)
) default CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__phocaemail_subscriber_lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_subscriber` int(11) NOT NULL DEFAULT '0',
  `id_list` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_subscriber` (`id_subscriber`),
  KEY `id_list` (`id_list`) 
) DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `#__phocaemail_newsletter_lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_newsletter` int(11) NOT NULL DEFAULT '0',
  `id_list` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_newsletter` (`id_newsletter`),
  KEY `id_list` (`id_list`) 
) DEFAULT CHARSET=utf8 ;

