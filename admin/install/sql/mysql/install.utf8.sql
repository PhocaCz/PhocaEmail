CREATE TABLE IF NOT EXISTS `#__phocaemail_subscribers` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(250) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `name` varchar(250) NOT NULL default '',
  `email` varchar(250) NOT NULL default '',
  `userid` int(11) NOT NULL default '0',
  `type` tinyint(3) NOT NULL default '0',
  `token` char(64) default NULL,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_unsubscribe` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_register` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_active` datetime NOT NULL default '0000-00-00 00:00:00',
  `hits` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL default '0',
  `active` tinyint(1) NOT NULL default '0',
  `registered` tinyint(1) NOT NULL default '0',
  `format` tinyint(1) NOT NULL default '0',
  `privacy` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `access` int(11) unsigned NOT NULL default '0',
  `params` text,
  `language` char(7) NOT NULL default '',
  PRIMARY KEY  (`id`)
) default CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__phocaemail_newsletters` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(250) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `subject` varchar(250) NOT NULL default '',
  `message` text,
  `message_html` text,
  `url` text,
  `token` char(64) default NULL,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `access` int(11) unsigned NOT NULL default '0',
  `params` text,
  `language` char(7) NOT NULL default '',
  PRIMARY KEY  (`id`)
) default CHARSET=utf8;

-- demo newsletter
INSERT INTO `#__phocaemail_newsletters` (`id`, `title`, `alias`, `subject`, `message`, `message_html`, `published`, `checked_out`, `checked_out_time`, `ordering`, `access`, `params`, `language`, `url`, `token`) VALUES
(3, 'Demo Newsletter', '', '{subscriptionname}: Demo Newsletter', NULL, '<div style="margin: 0; padding: 0; width: 100%; background: #f0f0f0; font-family: Arial, Helvetica, sans-serif;">\r\n<div style="padding: 5% 10%; margin: 0 auto; background: #f0f0f0; text-align: center;">\r\n<div style="padding: 3%; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; background: #fff; width: 90%; text-align: left;">\r\n<table style="width: 100%;">\r\n<tbody>\r\n<tr>\r\n<td>\r\n<div>Hello {name},<br /><br /> this is a demo of newsletter which can be created and sent to users by <a href="https://www.phoca.cz/phocaemail">Phoca Email</a> component. A Joomla! CMS component.</div>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td>\r\n<div>In this email content (even in email subject), you can use following variables:</div>\r\n<ul>\r\n<li><b>{name}</b> - will be replaced by user name</li>\r\n<li><b>{email}</b> - will be replaced by user email</li>\r\n<li><b>{sitename}</b> - will be replaced by website name - can be set in options</li>\r\n<li><b>{subscriptionname}</b> - will be replaced by subscription name - can be set in options</li>\r\n<li><b>{activationlink}</b> - will be replaced by activation link - this parameter should be used in activation email and should be set in HREF attribute of A tag - see default settings for Activation Email parameter in Phoca Email Options</li>\r\n<li><b>{unsubscribelink}</b> - will be replaced by unsubscribe link - this parameter should be included in every newsletter and should be set in HREF attribute of A tag</li>\r\n<li><b>{articlelink}</b> - will be replaced by link to specific article set in newsletter options</li>\r\n<li><b>{readonlinelink}</b> - will be replaced by link to newsletter displayed online - it can be used e.g. for displaying "Click here to read online" link - should be used in HREF attribute of A tag</li>\r\n</ul>\r\n<div>Variables will be automatically replaced when sending email.</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<br />\r\n<table style="width: 100%;">\r\n<tbody>\r\n<tr>\r\n<td style="width: 50%; background: #FFCC33; color: be920f; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; vertical-align: top;">\r\n<div style="padding: 10px;">\r\n<ul>\r\n<li><a style="color: be920f;" href="https://www.phoca.cz/phocaemail">Phoca Email component</a></li>\r\n<li><a style="color: be920f;" href="https://www.phoca.cz/phoca-email-newsletter-module">Phoca Email Newsletter module</a></li>\r\n<li><a style="color: be920f;" href="https://www.phoca.cz/documentation/category/60-phoca-email-component">Phoca Email documentation</a></li>\r\n<li><a style="color: be920f;" href="https://www.phoca.cz/download/category/47-phoca-email-component">Phoca Email download</a></li>\r\n<li><a style="color: be920f;" href="https://www.phoca.cz/forum">Phoca Email forum</a></li>\r\n</ul>\r\n</div>\r\n</td>\r\n<td style="width: 50%; background: #FF7d33; color: be4e0f; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; vertical-align: top;">\r\n<div style="padding: 10px;">\r\n<ul>\r\n<li><a style="color: be4e0f;" href="https://www.phoca.cz/">Phoca website</a></li>\r\n<li><a style="color: be4e0f;" href="https://www.phoca.cz/documentation">Phoca documentation website</a></li>\r\n<li><a style="color: be4e0f;" href="https://www.phoca.cz/download">Phoca download website</a></li>\r\n<li><a style="color: be4e0f;" href="https://www.phoca.cz/joomla3demo">Phoca demo website</a></li>\r\n<li><a style="color: be4e0f;" href="https://www.phoca.cz/forum">Phoca forum website</a></li>\r\n</ul>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<br />\r\n<div><a href="{readonlinelink}">Click here to read online</a></div>\r\n<br />\r\n<div><a href="{unsubscribelink}">Click here to unsubscribe</a></div>\r\n</div>\r\n</div>\r\n</div>', 1, 0, '0000-00-00 00:00:00', 0, 1, NULL, '', 'https://www.phoca.cz', '9f60904998edfbf98e70e5256442e566375261a215d12179dd068d7ee0e34dc1');

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
