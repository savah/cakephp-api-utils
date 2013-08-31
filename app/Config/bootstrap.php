<?php

App::uses('AppError', 'Lib');
App::uses('AppUtility', 'Lib' . DS . 'Utility');

CakePlugin::load('OAuth2', array('bootstrap' => true, 'routes' => false)); // we override these routes with our own
CakePlugin::load('Api');
CakePlugin::load('VirtualHabtm');
CakePlugin::load('DataTypeJuggling');
CakePlugin::load('SpecialFields');
CakePlugin::load('NullableForeignIds');

Configure::write('Dispatcher.filters', array(
	'AssetDispatcher',
	'CacheDispatcher',
	'Api.ApiCorsPreflightDispatcher',
	'Api.ApiCustomHeadersDispatcher'
));

App::uses('CakeLog', 'Log');
CakeLog::config('debug', array(
	'engine' => 'FileLog',
	'types' => array('notice', 'info', 'debug'),
	'file' => 'debug',
));
CakeLog::config('error', array(
	'engine' => 'FileLog',
	'types' => array('warning', 'error', 'critical', 'alert', 'emergency'),
	'file' => 'error',
));