<?php

// Public users can add new users; default users can access all verbs
$config = $setActions($config, 'Users', array(
	'Public' => array(
		'allow' => '(add)'
	),
	'Default' => array(
		'allow' => '*'
	)
));

$config = $setCrud($config, 'User', array(
	'Public' => array( // Public users can create a new user, but nothing else
		'username' => array('create'),
		'password' => array('create'),
		'displayName' => array('create'),
		'email' => array('create')
	),
	'Default' => array( // Default users can create other users or update their information
		'id' => array('read'),
		'username',
		'password' => array('create', 'update'),
		'displayName',
		'email',
		'created' => array('read'), // `created` is only readable
		'modified' => array('read') // `modified` is only readable
	),
	'Admin' => array( // Admins & higher can interact with role data
		'role.id',
	)
));