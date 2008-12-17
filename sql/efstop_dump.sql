CREATE TABLE `accounts` (
  `id` int(11) NOT NULL auto_increment,
  `key` varchar(50) default NULL,
  `account_name` varchar(50) default NULL,
  `startdate` datetime default NULL,
  `enddate` datetime default NULL,
  `planid` int(11) default NULL,
  `logo_file` varchar(200) default NULL,
  `creatorid` int(11) default NULL,
  `updaterid` int(11) default NULL,
  `disabled` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Dump of table ci_sessions
# ------------------------------------------------------------

CREATE TABLE `ci_sessions` (
  `session_id` varchar(40) NOT NULL default '0',
  `ip_address` varchar(16) NOT NULL default '0',
  `user_agent` varchar(50) NOT NULL,
  `referrer` text,
  `last_activity` int(10) unsigned NOT NULL default '0',
  `session_data` text NOT NULL,
  PRIMARY KEY  (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `collections` (
  `id` int(11) NOT NULL auto_increment,
  `collection` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `collections_tags` (
  `id` int(11) NOT NULL auto_increment,
  `collections_id` int(11) default NULL,
  `tags_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `colours` (
  `id` int(11) NOT NULL auto_increment,
  `colorcode` varchar(7) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `colours_image` (
  `id` int(11) NOT NULL auto_increment,
  `coloursid` int(11) default NULL,
  `imagesid` int(11) default NULL,
  `quantity` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `comments` (
  `id` int(11) NOT NULL auto_increment,
  `comment` text,
  `imageid` int(11) default NULL,
  `userid` int(11) NOT NULL,
  `datecreated` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `friendly_urls` (
  `id` int(11) NOT NULL auto_increment,
  `friendly_url` varchar(100) default NULL,
  `content_type` varchar(25) default NULL,
  `contentid` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `groups` (
  `id` int(11) NOT NULL auto_increment,
  `groupname` varchar(20) default NULL,
  `grouptitle` varchar(150) default NULL,
  `active` tinyint(1) default NULL,
  `groupsetsid` int(11) default NULL,
  `datecreated` datetime default NULL,
  `dateupdated` datetime default NULL,
  `creatorid` int(11) default NULL,
  `updaterid` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `groupsets` (
  `id` int(11) NOT NULL auto_increment,
  `setname` varchar(30) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `images` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(200) default NULL,
  `imagecode` varchar(20) default NULL,
  `filename` varchar(200) default NULL,
  `previewname` varchar(200) default NULL,
  `thumbname` varchar(200) default NULL,
  `width` int(11) default NULL,
  `height` int(11) default NULL,
  `orientation` enum('P','L','S') NOT NULL default 'P',
  `filesize` int(11) default NULL,
  `description` text,
  `imagesetid` int(11) NOT NULL default '0',
  `datecreated` datetime default NULL,
  `creatorid` int(11) default NULL,
  `updaterid` int(11) default NULL,
  `s3upload` tinyint(1) NOT NULL default '0',
  `accountid` int(11) NOT NULL default '0',
  `exif` text,
  `processed` tinyint(1) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `images_lightbox` (
  `id` int(11) NOT NULL auto_increment,
  `imagesid` int(11) default NULL,
  `lightboxid` int(11) default NULL,
  `creatorid` int(11) default NULL,
  `datecreated` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `imagesearch` (
  `id` int(11) unsigned NOT NULL default '0',
  `title` varchar(200) NOT NULL default '',
  `description` text NOT NULL,
  FULLTEXT KEY `title` (`title`,`description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `imagesets` (
  `id` int(11) NOT NULL auto_increment,
  `setname` varchar(100) default NULL,
  `creatorid` int(11) default NULL,
  `datecreated` datetime default NULL,
  `updaterid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `imagesets_access` (
  `id` int(11) NOT NULL auto_increment,
  `imagesetsid` int(11) default NULL,
  `usersid` int(11) default NULL,
  `groupsid` int(11) default NULL,
  `full` tinyint(1) NOT NULL default '0',
  `datecreated` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `lightbox` (
  `id` int(11) NOT NULL auto_increment,
  `boxtitle` varchar(100) default NULL,
  `creatorid` int(11) default NULL,
  `datecreated` datetime default NULL,
  `updaterid` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `lightbox_access` (
  `id` int(11) NOT NULL auto_increment,
  `lightboxid` int(11) default NULL,
  `usersid` int(11) default NULL,
  `groupsid` int(11) default NULL,
  `full` tinyint(1) NOT NULL default '0',
  `guestkey` varchar(32) default NULL,
  `emailaddress` varchar(100) default NULL,
  `datecreated` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL auto_increment,
  `notification_ident` varchar(30) default NULL,
  `notificationtitle` varchar(100) default NULL,
  `notificationtext` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `notifications` (`id`,`notification_ident`,`notificationtitle`,`notificationtext`) VALUES ('1','home_intro','Welcome To efstop','<p>Thanks for signing up to efstop.  As you\'re probably aware of by now, efstop provides you with a simple online method for storing your image database and retrieving them quickly and easily.</p>\n<p>Here on the home page you\'ll see your own lightboxes as well as any shared with you by others, your recently upload images and a listing of all your images sets.</p>\n<p>Down at the foot of the page you\'ll see the lightbox bar.  From here you can quickly access any of your current lightboxes or create new ones.  Clicking on \'Current Lighbox\' will open the tray and show you the image within your lightbox.</p>\n<p>Get started by <a href=\"http://app.getefstop.net/dam_controllers/image/upload\">uploading some images</a>.</p>');
INSERT INTO `notifications` (`id`,`notification_ident`,`notificationtitle`,`notificationtext`) VALUES ('2','imagesets','Image Sets','<p>Treat image sets as ways of bounding together a collection of related photos.  They may all be from a particular photo shoot or from the same trip.</p>');
INSERT INTO `notifications` (`id`,`notification_ident`,`notificationtitle`,`notificationtext`) VALUES ('3','imageset','Image Sets','<p>Organise your images by keeping similar ones together.</p>');

CREATE TABLE `notifications_dismiss` (
  `notificationsid` int(11) default NULL,
  `usersid` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `savedsearches` (
  `id` int(11) NOT NULL auto_increment,
  `searchtitle` varchar(100) default NULL,
  `searchTerms` varchar(250) default NULL,
  `searchOrientation` varchar(1) default NULL,
  `searchImagesets` varchar(250) default NULL,
  `searchTags` varchar(250) default NULL,
  `usersid` int(11) default NULL,
  `updaterid` int(11) default NULL,
  `creatorid` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tagged` (
  `id` int(11) NOT NULL auto_increment,
  `itemid` int(11) default NULL,
  `itemtype` varchar(50) default NULL,
  `tag_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tags` (
  `id` int(11) NOT NULL auto_increment,
  `tag` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(50) default NULL,
  `password` varchar(150) default NULL,
  `emailaddress` varchar(200) default NULL,
  `firstname` varchar(100) default NULL,
  `lastname` varchar(100) default NULL,
  `profiletype` varchar(10) default NULL,
  `usertype` enum('super','admin','limited') NOT NULL default 'limited',
  `lastlogin` datetime default NULL,
  `active` tinyint(1) NOT NULL default '1',
  `deleted` tinyint(1) NOT NULL default '0',
  `datecreated` datetime default NULL,
  `dateupdated` datetime default NULL,
  `creatorid` int(11) default NULL,
  `updaterid` int(11) default NULL,
  `resetbee` varchar(50) default NULL,
  `openid` varchar(100) default NULL,
  `accountid` int(11) default NULL,
  `openid_identifier` varchar(250) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users_groups` (
  `id` int(11) NOT NULL auto_increment,
  `usersid` int(11) default NULL,
  `groupsid` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users_groupsets` (
  `id` int(11) NOT NULL auto_increment,
  `usersid` int(11) default NULL,
  `groupsetsid` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
