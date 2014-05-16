CREATE TABLE  `categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned NOT NULL,
  `name` varchar(30) NOT NULL,
  `color` varchar(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE  `group_users` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `permission` tinyint(4) NOT NULL,
  PRIMARY KEY (`user_id`,`group_id`),
  KEY `FK_group_id_groups` (`group_id`),
  KEY `TK_user_id_users` (`user_id`),
  CONSTRAINT `FK_group_id_groups` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`),
  CONSTRAINT `FK_user_id_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE  `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mapping_id` varchar(45) NOT NULL,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE  `pad_categories` (
  `category_id` int(11) unsigned NOT NULL,
  `pad_id` varchar(100) NOT NULL,
  `pad_category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`pad_category_id`) USING BTREE,
  KEY `categoryPad_index` (`category_id`,`pad_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE  `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(200) NOT NULL,
  `password` varchar(80) NOT NULL,
  `name` varchar(100) NOT NULL,
  `verified_email` tinyint(1) NOT NULL,
  `tokenhash` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;