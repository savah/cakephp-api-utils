<?php
require(APP . 'Config' . DS . 'Acl' . DS . '_base_acl.php');

/**
 * Bootstrap Models Access Control Lists
 *
 * PHP 5
 *
 * Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @since       1.0
 * @package     Api
 * @subpackage  Api.Config.Acl
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */

$setActions = function($config, $controller, $array = array()) {
	foreach ($array as $role => $permissions) {
		foreach ($permissions as $permission => $action) {
			$config['rules'][$permission]['controllers/' . $controller . '/' . $action] = $role;
		}
	}
	return $config;
};

$setCrud = function($config, $model, $array = array()) {
	foreach ($array as $role => $fields) {
		foreach ($fields as $key => $field) {
			$permissions = array(
				'allow' => array('create', 'read', 'update'),
				'deny' => array()
			);
			if (is_array($field)) {
				$field_permissions = $field;
				foreach ($permissions['allow'] as $permission) {
					if (!in_array($permission, $field_permissions)) {
						$permissions['deny'][] = $permission;
						unset($permissions['allow'][array_search($permission, $permissions['allow'])]);
					}
				}
				$field = $key;
			}
			foreach ($permissions as $permission => $crud) {
				if (!empty($crud)) {
					foreach ($crud as $type) {
						if (!empty($config['rules'][$permission]['crud/' . $model . '/' . $type . '/' . $field])) {
							$current_role = $config['rules'][$permission]['crud/' . $model . '/' . $type . '/' . $field];
							$new_role = $role;
							if (
								$config['hierarchy'][$new_role] > $config['hierarchy'][$current_role] &&
								$permission === 'allow'
							) {
								continue;
							}
						}
						$config['rules'][$permission]['crud/' . $model . '/' . $type . '/' . $field] = $role;
					}
				}
			}
		}
	}
	return $config;
};

if (!empty($models)) {
	foreach ($models as $model) {
		$model_file = APP . 'Config' . DS . 'Acl' . DS . $model . '.php';
		if (file_exists($model_file)) {
			require $model_file;
		}
	}
}
