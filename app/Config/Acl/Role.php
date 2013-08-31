<?php

// Only allow users to access to read verbs
$config = $setActions($config, 'Roles', array(
	'Default' => array( // Default users can only view role data
		'allow' => '(index|view)'
	)
));

$config = $setCrud($config, 'Role', array(
	'Default' => array( // Default users can only view role data
		'id' => array('read'),
		'name' => array('read')
	)
));