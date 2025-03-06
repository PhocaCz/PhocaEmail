ALTER TABLE `#__phocaemail_subscribers` ADD COLUMN `privacy` tinyint(1) NOT NULL default '0';
ALTER TABLE `#__phocaemail_subscribers` ADD COLUMN `date_register` datetime NOT NULL default '0000-00-00 00:00:00';
ALTER TABLE `#__phocaemail_subscribers` ADD COLUMN `date_active` datetime NOT NULL default '0000-00-00 00:00:00';
