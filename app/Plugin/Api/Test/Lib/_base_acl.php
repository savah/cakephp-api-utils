<?php
/**
 * Test file used with ApiPhpAcl Class tests
 *
 * @author  Everton Yoshitani <everton@wizehive.com>
 * @since   1.0
 */

/**
 * The role map defines how to resolve the user record from your application
 * to the roles you defined in the roles configuration.
 */
$config['map'] = array(
	'User' => 'User/id',
	'Role' => 'User/role_id',
);

/**
 * define aliases to map your model information to
 * the roles defined in your role configuration.
 */
$config['alias'] = array(
	'Role/public' => 'Public',
	'Role/1' => 'Default',
	'Role/2' => 'Admin',
	'Role/999' => 'Root'
);

/**
 * role configuration
 */
$config['roles'] = array(
	'Public' => null,
	'Default' => 'Public',
	'Admin' => 'Default',
	'Root' => null
);

/**
 * rule configuration
 */
$config['rules'] = array(
	'allow' => array(
		
		'*' => 'Root'
		
	),
	'deny' => array()
);