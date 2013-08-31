<?php
/**
 * OAuth2 Storage Utility
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
 * @subpackage  OAuth2.Lib
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
class OAuth2Storage implements 
	OAuth2_Storage_AuthorizationCodeInterface,
	OAuth2_Storage_AccessTokenInterface,
	OAuth2_Storage_ClientCredentialsInterface,
	OAuth2_Storage_RefreshTokenInterface,
	OAuth2_Storage_UserCredentialsInterface {
	
	/**
	 * Settings
	 * 
	 * @since   0.1
	 * @var	    array
	 */
	protected $_settings = array(
		'userModel' => 'User'
	);
	
	/**
	 * Constructor
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	0.1
	 * @param	array	$settings
	 * @return	void
	 */
	public function __construct($settings = array()) {
		
		if (!empty($settings)) {
			$this->_settings = array_merge($this->_settings, $settings);
		}
		
	}
	
	/**
	 * Get an `OAuth2.*` model object from the `ClassRegistry`
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	0.1
	 * @param	string	$modelName
	 * @return	void
	 */
	public function model($modelName) {
		return ClassRegistry::init('OAuth2.OAuth2' . $modelName);
	}
	
    /**
	 * Fetch authorization code data (probably the most common grant type).
	 *
	 * Retrieve the stored data for the given authorization code.
	 *
	 * Required for OAuth2::GRANT_TYPE_AUTH_CODE.
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	0.1
	 * @param	string	$code	Authorization code to be check with.
	 * @return	array	An associative array as below, and NULL if the code is invalid:
	 *					- client_id: Stored client identifier.
	 *					- redirect_uri: Stored redirect URI.
	 *					- expires: Stored expiration in unix timestamp.
	 *					- scope: (optional) Stored scope values in space-separated string.
	 *
	 * @see http://tools.ietf.org/html/rfc6749#section-4.1
	 *
	 * @ingroup oauth2_section_4
	 */
	public function getAuthorizationCode($code) {
		
		return $this->model('AuthCode')->getAuthorizationCode($code);
		
	}

	/**
	 * Take the provided authorization code values and store them somewhere.
	 *
	 * This function should be the storage counterpart to getAuthCode().
	 *
	 * If storage fails for some reason, we're not currently checking for
	 * any sort of success/failure, so you should bail out of the script
	 * and provide a descriptive fail message.
	 *
	 * Required for OAuth2::GRANT_TYPE_AUTH_CODE.
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	0.1
	 * @param	string	$code			Authorization code to be stored.
	 * @param	string	$client_id		Client identifier to be stored.
	 * @param	integer	$user_id		User identifier to be stored.
	 * @param	string	$redirect_uri	Redirect URI to be stored.
	 * @param	integer	$expires		Expiration to be stored.
	 * @param	string	$scope			(optional) Scopes to be stored in space-separated string.
	 * @return	boolean
	 * 
	 * @ingroup oauth2_section_4
	 */
	public function setAuthorizationCode($code, $client_id, $user_id, $redirect_uri, $expires, $scope = null) {
		
		return $this->model('AuthCode')->setAuthorizationCode($code, $client_id, $user_id, $redirect_uri, $expires, $scope);
		
	}

	/**
	 * Once an Authorization Code is used, it must be exipired
	 *
	 * @see http://tools.ietf.org/html/rfc6749#section-4.1.2
	 *
	 *    The client MUST NOT use the authorization code
	 *    more than once.  If an authorization code is used more than
	 *    once, the authorization server MUST deny the request and SHOULD
	 *    revoke (when possible) all tokens previously issued based on
	 *    that authorization code
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	0.1
	 * @param	string	$code	Authorization code to be expired.
	 * @return	boolean
	 *
	 */
	public function expireAuthorizationCode($code) {
		
		return $this->model('AuthCode')->expireAuthorizationCode($code);
		
	}

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
		
		return $this->model('AccessToken')->getAccessToken($oauth_token);
		
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

		return $this->model('AccessToken')->setAccessToken($oauth_token, $client_id, $user_id, $expires, $scope);
		
	}

	/**
	 * Make sure that the client credentials is valid.
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	0.1
	 * @param	string	$client_id		Client identifier to be check with.
	 * @param	string	$client_secret	(optional) If a secret is required, check that they've given the right one.
	 * @return	boolean	TRUE if the client credentials are valid, and MUST return FALSE if it isn't.
	 *
	 * @see http://tools.ietf.org/html/rfc6749#section-3.1
	 *
	 * @ingroup oauth2_section_3
	 */
	public function checkClientCredentials($client_id, $client_secret = null) {
		
		return $this->model('Client')->checkClientCredentials($client_id, $client_secret);
		
	}

	/**
	 * Check restricted grant types of corresponding client identifier.
	 *
	 * If you want to restrict clients to certain grant types, override this
	 * function.
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	0.1
	 * @param	string	$client_id	Client identifier to be check with.
	 * @param	string	$grant_type	Grant type to be check with
	 * @return	boolean	TRUE if the grant type is supported by this client identifier, and
	 *					FALSE if it isn't.
	 * @ingroup oauth2_section_4
	 */
	public function checkRestrictedGrantType($client_id, $grant_type) {
		
		return true;
		
	}

	/**
	 * Get client details corresponding client_id.
	 *
	 * OAuth says we should store request URIs for each registered client.
	 * Implement this function to grab the stored URI for a given client id.
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	0.1
	 * @param	string	$client_id	Client identifier to be check with.
	 * @return	array	Client details. Only mandatory item is the "registered redirect URI", and MUST
	 *					return FALSE if the given client does not exist or is invalid.
	 *
	 * @ingroup oauth2_section_4
	 */
	public function getClientDetails($client_id) {
		
		return $this->model('Client')->getClientDetails($client_id);
		
	}

	/**
	 * Grant refresh access tokens.
	 *
	 * Retrieve the stored data for the given refresh token.
	 *
	 * Required for OAuth2::GRANT_TYPE_REFRESH_TOKEN.
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	0.1
	 * @param	string	$refresh_token	Refresh token to be check with.
	 * @return	array	An associative array as below, and NULL if the refresh_token is
	 *					invalid:
	 *					- client_id: Stored client identifier.
	 *					- expires: Stored expiration unix timestamp.
	 *					- scope: (optional) Stored scope values in space-separated string.
	 *
	 * @see http://tools.ietf.org/html/rfc6749#section-6
	 *
	 * @ingroup oauth2_section_6
	 */
	public function getRefreshToken($refresh_token) {
		
		return $this->model('RefreshToken')->getRefreshToken($refresh_token);
		
	}

	/**
	 * Take the provided refresh token values and store them somewhere.
	 *
	 * This function should be the storage counterpart to getRefreshToken().
	 *
	 * If storage fails for some reason, we're not currently checking for
	 * any sort of success/failure, so you should bail out of the script
	 * and provide a descriptive fail message.
	 *
	 * Required for OAuth2::GRANT_TYPE_REFRESH_TOKEN.
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	0.1
	 * @param	string	$refresh_token	Refresh token to be stored.
	 * @param	string	$client_id		Client identifier to be stored.
	 * @param	integer	$expires		expires to be stored.
	 * @param	string	$scope			(optional) Scopes to be stored in space-separated string.
	 * @return	boolean
	 * 
	 * @ingroup oauth2_section_6
	 */
	public function setRefreshToken($refresh_token, $client_id, $user_id, $expires, $scope = null) {
		
		return $this->model('RefreshToken')->setRefreshToken($refresh_token, $client_id, $user_id, $expires, $scope);
		
	}

	/**
	 * Expire a used refresh token.
	 *
	 * This is not explicitly required in the spec, but is almost implied.
	 * After granting a new refresh token, the old one is no longer useful and
	 * so should be forcibly expired in the data store so it can't be used again.
	 *
	 * If storage fails for some reason, we're not currently checking for
	 * any sort of success/failure, so you should bail out of the script
	 * and provide a descriptive fail message.
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	0.1
	 * @param	string	$refresh_token	Refresh token to be expirse.
	 * @return	boolean
	 * 
	 * @ingroup oauth2_section_6
	 */
	public function unsetRefreshToken($refresh_token) {
		
		return $this->model('RefreshToken')->unsetRefreshToken($refresh_token);
		
	}

	/**
	 * Grant access tokens for basic user credentials.
	 *
	 * Check the supplied username and password for validity.
	 *
	 * You can also use the $client_id param to do any checks required based
	 * on a client, if you need that.
	 *
	 * Required for OAuth2::GRANT_TYPE_USER_CREDENTIALS.
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	0.1
	 * @param	string	$username	Username to be check with.
	 * @param	string	$password	Password to be check with.
	 * @return	boolean	TRUE if the username and password are valid, and FALSE if it isn't.
	 *					Moreover, if the username and password are valid, and you want to
	 *					verify the scope of a user's access, return an associative array
	 *					with the scope values as below. We'll check the scope you provide
	 *					against the requested scope before providing an access token:
	 * @code
	 * return array(
	 * 'scope' => <stored scope values (space-separated string)>,
	 * );
	 * @endcode
	 *
	 * @see http://tools.ietf.org/html/rfc6749#section-4.3
	 *
	 * @ingroup oauth2_section_4
	 */
	public function checkUserCredentials($username, $password) {
		
		$modelName = $this->_settings['userModel'];
		
		if (!isset($this->{$modelName})) {
			$this->{$modelName} = ClassRegistry::init($modelName);
		}
		
		return (bool) $this->{$modelName}->authenticate($username, $password);
		
	}

	/**
	 * Get user details
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since	0.1
	 * @param	string	$username
	 * @return	array	ARRAY the associated "scope" or "user_id" values if applicable, or an empty array
	 *					if this does not apply
	 *
	 */
	public function getUserDetails($username) {
		
		$modelName = $this->_settings['userModel'];
		
		if (!isset($this->{$modelName})) {
			$this->{$modelName} = ClassRegistry::init($modelName);
		}
		
		if (method_exists($this->{$modelName}, 'getUserIdByUsername')) {
			$user_id = $this->{$modelName}->getUserIdByUsername($username);
		}

		if (empty($user_id)) {
			return array();
		}

		return compact('user_id');
		
	}

}
