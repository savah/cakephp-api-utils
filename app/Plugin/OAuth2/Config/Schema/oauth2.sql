/**
 * OAuth2 Schema
 *
 *
 * @author		Anthony Putignano <anthony@wizehive.com>
 * @since		0.1
 * @package		OAuth2
 */

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