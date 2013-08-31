<?php
/**
 * Bootstrap
 *
 * In order for this to work, you must boot up the plugin in `core.php` like so:
 * `CakePlugin::load('OAuth2', array('bootstrap' => true, 'routes' => true));`
 *
 * PHP 5
 *
 * Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @author      Anthony Putignano <anthony@wizehive.com>
 * @since       0.1
 * @package     OAuth2
 * @subpackage  OAuth2.Config
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::import(
	'Vendor', 
	'OAuth2.OAuth2Autoloader', 
	array('file' => 'OAuth2ServerPHP' . DS . 'src' . DS . 'OAuth2' . DS . 'Autoloader.php')
);
OAuth2_Autoloader::register();
