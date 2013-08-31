<?php

// Allow a default user to access all verbs
$config = $setActions($config, 'Comments', array(
	'Default' => array(
		'allow' => '*'
	)
));

$config = $setCrud($config, 'Comment', array(
	'Default' => array(
		'id' => array('read'),
		'post.id',
		'user.id' => array('read'), // the `user.id` which created the comment is only readable
		'body',
		'created' => array('read'), // `created` is only readable
		'modified' => array('read') // `modified` is only readable
	)
));