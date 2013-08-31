<?php
App::uses('ResourceRoute', 'Lib/Routing');

if ('auth.api-demo.dev' === env('HTTP_HOST')) {

	Router::connect('/oauth2/:action/*', array('controller' => 'AppOAuth2', 'plugin' => null));
	Router::connect('/users/login', array('controller' => 'users', 'action' => 'login'));
	Router::connect('/users/signup', array('controller' => 'users', 'action' => 'signup'));
	Router::connect('/users/logout', array('controller' => 'users', 'action' => 'logout'));

} else {

	Router::connect('/users/me/*', array('controller' => 'users', 'action' => 'me'));

	ResourceRoute::mapAll(array(
		'users' => array(
			'modelAlias' => 'User'
		),
		'roles' => array(
			'modelAlias' => 'Role'
		),
		'posts' => array(
			'modelAlias' => 'Post',
			'comments' => array(
				'controllerClass' => 'comments',
				'modelAlias' => 'Comments'
			),
		)
	));

}

Router::parseExtensions('json', 'xml', 'csv');
CakePlugin::routes();