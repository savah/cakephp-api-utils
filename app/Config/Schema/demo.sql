DROP TABLE IF EXISTS `comments`;

CREATE TABLE `comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `body` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `oauth2_access_tokens`;

CREATE TABLE `oauth2_access_tokens` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `access_token` char(128) NOT NULL DEFAULT '',
  `oauth2_client_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `expires` int(11) NOT NULL,
  `scope` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `access_token_idx` (`access_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `oauth2_auth_codes`;

CREATE TABLE `oauth2_auth_codes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `auth_code` char(128) NOT NULL DEFAULT '',
  `oauth2_client_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `redirect_uri` varchar(200) NOT NULL,
  `expires` int(11) NOT NULL,
  `scope` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `auth_code_idx` (`auth_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `oauth2_authorizations`;

CREATE TABLE `oauth2_authorizations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `oauth2_client_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `scope` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth2_client_user_idx` (`oauth2_client_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `oauth2_clients`;

CREATE TABLE `oauth2_clients` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `api_key` char(36) NOT NULL DEFAULT '',
  `user_id` int(11) unsigned DEFAULT NULL,
  `api_secret` char(128) NOT NULL DEFAULT '',
  `app_name` varchar(50) NOT NULL DEFAULT '',
  `redirect_uri` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `api_key_api_secret_idx` (`api_key`,`api_secret`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `oauth2_refresh_tokens`;

CREATE TABLE `oauth2_refresh_tokens` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `refresh_token` char(128) NOT NULL DEFAULT '',
  `oauth2_client_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `expires` int(11) NOT NULL,
  `scope` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `refresh_token_idx` (`refresh_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `posts`;

CREATE TABLE `posts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) unsigned NOT NULL DEFAULT '1',
  `username` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `display_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `oauth2_clients` (`api_key`, `user_id`, `api_secret`, `app_name`, `redirect_uri`, `created`, `modified`)
VALUES
  ('demo-client-flow-key',1,'59700c64729d53b473876124a1a427102dfc2f1a7f8e6f96e812563e2e46af229e7d3256073b1ac0ee828fb11b0e9cc167d6cd5c60bb91689450d1976ebe6f6d','Demo Client Flow App','http://client.api-demo.dev/',NOW(),NOW());

INSERT INTO `roles` (`id`, `slug`, `name`) VALUES ('1', 'default', 'Default');
INSERT INTO `roles` (`id`, `slug`, `name`) VALUES ('2', 'admin', 'Admin');
INSERT INTO `roles` (`id`, `slug`, `name`) VALUES ('999', 'root', 'Root');