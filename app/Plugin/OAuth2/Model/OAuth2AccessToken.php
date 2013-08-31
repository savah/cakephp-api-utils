<?php
App::uses('OAuth2AppModel', 'OAuth2.Model');

/**
 * OAuth2 Access Token Model
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
 * @subpackage  OAuth2.Model
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
class OAuth2AccessToken extends OAuth2AppModel {
	
	/**
	 * Required model
	 * 
	 * @since   0.1
	 * @var	    string 
	 */
	public $useTable = 'oauth2_access_tokens';
	
	/**
	 * Behaviors
	 *
	 * @since   0.1
	 * @var	    array 
	 */
	public $actsAs = array(
		'OAuth2.OAuth2Hash' => array(
			'fields' => array('access_token')
		),
		'OAuth2.OAuth2Authorization' => array(
			'fields' => array(
				'client_id' => 'oauth2_client_id',
				'user_id' => 'user_id',
				'scope' => 'scope'
			)
		)
	);
	
	/**
	 * Belongs to associations
	 *
	 * @since   0.1
	 * @var	    array 
	 */
	public $belongsTo = array(
		'OAuth2Client' => array(
			'className' => 'OAuth2.OAuth2Client',
			'foreignKey' => 'oauth2_client_id'
		)
	);
	
	/**
	 * Look up the supplied oauth_token from storage.
	 *
	 * We need to retrieve access token data as we create and verify tokens.
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	0.1
	 * @param	string	$oauth_token	oauth_token to be check with.
	 * @return	array	An associative array as below, and return NULL if the supplied oauth_token
	 *					is invalid:
	 *					- client_id: Stored client identifier.
	 *					- expires: Stored expiration in unix timestamp.
	 *					- scope: (optional) Stored scope values in space-separated string.
	 *
	 * @ingroup oauth2_section_7
	 */
	public function getAccessToken($oauth_token) {
		
		$data = $this->find('first', array(
			'conditions' => array(
				$this->alias . '.access_token' => $oauth_token
			),
			'fields' => array(
				'expires',
				'scope'
			),
			'contain' => array(
				'OAuth2Client' => array(
					'fields' => array(
						'api_key'
					)
				)
			)
		));
		
		if (
			empty($data[$this->alias]) ||
			empty($data['OAuth2Client']['api_key'])
		) {
			return null;
		}
		
		return array(
			'client_id' => $data['OAuth2Client']['api_key'],
			'expires' => $data[$this->alias]['expires'],
			'scope' => $data[$this->alias]['scope']
		);
		
	}
	
	/**
	 * Store the supplied access token values to storage.
	 *
	 * We need to store access token data as we create and verify tokens.
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	0.1
	 * @param	string	$oauth_token	oauth_token to be stored.
	 * @param	string	$client_id		Client identifier to be stored.
	 * @param	integer	$user_id		User identifier to be stored.
	 * @param	integer	$expires		Expiration to be stored.
	 * @param	string	$scope			(optional) Scopes to be stored in space-separated string.
	 * @return	boolean
	 * 
	 * @ingroup oauth2_section_4
	 */
	public function setAccessToken($oauth_token, $client_id, $user_id, $expires, $scope = null) {

		$client_id = $this->OAuth2Client->field('id', array('api_key' => $client_id));
		
		if (empty($client_id)) {
			return false;
		}
		
		$this->create(false);
		if (!$this->save(array(
			$this->alias => array(
				'access_token' => $oauth_token,
				'oauth2_client_id' => $client_id,
				'user_id' => $user_id,
				'expires' => $expires,
				'scope' => $scope
			)
		))) {
			return false;
		}
		
		return true;
		
	}
	
}
