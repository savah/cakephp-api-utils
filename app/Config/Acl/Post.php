<?php

// Allow a default user to access all verbs
$config = $setActions($config, 'Posts', array(
	'Default' => array(
		'allow' => '*'
	)
));

$config = $setCrud($config, 'Post', array(
	'Default' => array(
		'id' => array('read'),
		'user.id' => array('read'), // the `user.id` which created the post is only readable
		'title',
		'body',
		'created' => array('read'), // `created` is only readable
		'modified' => array('read') // `modified` is only readable
	)
));