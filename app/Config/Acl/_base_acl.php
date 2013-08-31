<?php

$config['map'] = array(
	'User' => 'User/id', // Where do we get the User ID from?
	'Role' => 'User/role_id', // Where do we get the User Role ID from?
);

// List of Role IDs to Role Names
$config['alias'] = array(
	'Role/0' => 'Public',
	'Role/1' => 'Default',
	'Role/2' => 'Admin',
	'Role/999' => 'Root'
);

// Role hierarchy
$config['roles'] = array(
	'Public' => null,
	'Default' => 'Public',
	'Admin' => 'Default',
	'Root' => null
);

// Role hierarcy... repetitive, but required for now. Needs improvement.
$config['hierarchy'] = array(
	'Public' => 0,
	'Default' => 1,
	'Admin' => 2,
	'Root' => 3
);

// Default ACL rules
$config['rules'] = array(
	'allow' => array(
		'*' => 'Root'
	),
	'deny' => array()
);