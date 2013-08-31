<?php
App::uses('Component', 'Controller');
App::uses('CakeTime', 'Utility');

/**
 * Api Query Component
 *
 * PHP 5
 *
 * Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @since       1.0
 * @package     Api
 * @subpackage  Api.Controller.Component
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
class ApiQueryComponent extends Component {
	
	/**
	 * Prefixes
	 *
	 * @since 	1.0
	 * @var 	array
	 */
	private $__prefixes = array(
		'not',
		'min',
		'max',
		'contains',
		'not-contains',
		'starts-with',
		'ends-with'
	);
	
	/**
	 * Cached route queries
	 *
	 * @since 	1.0
	 * @var 	array
	 */
	private $__cached_route_queries = null;
	
	/**
	 * Cached parent
	 *
	 * @since 	1.0
	 * @var 	array
	 */
	private $__cached_parent = null;
	
	/**
	 * Field map
	 *
	 * @since 	1.0
	 * @var 	array
	 */
	protected $_field_map = array();
	
	/**
	 * Model
	 *
	 * @since 	1.0
	 * @var 	string
	 */
	protected $_model = null;
	
	/**
	 * Passed Params
	 *
	 * @since 	1.0
	 * @var 	array
	 */
	protected $_passed_params = array();
	
	/**
	 * Field
	 *
	 * @since 	1.0
	 * @var 	string
	 */
	protected $_field = null;
	
	/**
	 * Value
	 *
	 * @since 	1.0
	 * @var 	string
	 */
	protected $_value = null;
	
	/**
	 * Protected Query Parameters
	 *
	 * These parameters are reserved and can't be used as conditions
	 *
	 * @since 	1.0
	 * @var 	array
	 */
	protected $_reserved_query_parameters = array(
		'limit', 'sort', 'page', 'direction', 'timezone', 'access_token'
	);
	
	/**
	 * Components
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @var     array
	 */
	public $components = array(
		'Auth'
	);
	
	/**
	 * Constructor
	 *
	 * @since   1.0
	 * @param   ComponentCollection $collection Collection object.
	 * @param   array $settings Component settings.
	 * @return  void
	 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		
		parent::__construct($collection, $settings);

		$this->Controller = $collection->getController();
		
	}
	
	/**
	 * On Model
	 *
	 * @since 	1.0
	 * @param 	string $model Model name 
	 * @return 	object
	 */
	public function onModel($model = null) {
		
		$this->_model = $model;
		
		return $this;
		
	}
	
	/**
	 * On Field
	 *
	 * @since 	1.0
	 * @param 	string $field Field name
	 * @return 	object
	 */
	public function onField($field = null) {
		
		$this->_field = $field;
		
		return $this;
		
	}
	
	/**
	 * With Value
	 *
	 * @since 	1.0
	 * @param 	string $value Value
	 * @return 	object
	 */
	public function withValue($value = null) {
		
		$this->_value = $value;
		
		return $this;
		
	}
	
	/**
	 * With Field Map
	 *
	 * @since 	1.0
	 * @param 	string $field_map Field map array. 
	 * @return 	object
	 */
	public function withFieldMap($field_map = array()) {
		
		$this->_field_map = $field_map;
		
		return $this;
		
	}
	
	/**
	 * With Passed Params
	 *
	 * @since 	1.0
	 * @param 	array $params Params 
	 * @return 	object
	 */
	public function withPassedParams($params = array()) {
		
		$this->_passed_params = $params;
		
		return $this;
		
	}
	
	/**
	 * Get Possible Query Prefixes
	 *
	 * @since 	1.0
	 * @return 	array
	 */
	public function prefixes() {
		
		return $this->__prefixes;
		
	}
	
	/**
	 * Fix underscores
	 *
	 * PHP converts dots in query keys to underscores.
	 * This method corrects that behavior.
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  object
	 */
	public function fixUnderscores() {
		
		if (empty($this->Controller->request->query) || !is_array($this->Controller->request->query)) {
			return $this;
		}
		
		foreach ($this->Controller->request->query as $key => $value) {
			if (
				!in_array($key, $this->_reserved_query_parameters) && 
				strpos($key, '_') !== false
			) {
				unset($this->Controller->request->query[$key]);
				$key = str_replace('_', '.', $key);
				$this->Controller->request->query[$key] = $value;
			}
		}
		
		return $this;
		
	}
	
	/**
	 * Parse Request Query Parameters to Model Conditions
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	array
	 */
	public function rendersConditions() {
		
		$field_map = $this->_field_map;
		$this->withFieldMap();
		
		if (empty($field_map)) {
			return array();
		}
		
		$query = $this->_passed_params;
		$this->withPassedParams();
		
		if (empty($query)) {
			$query = $this->Controller->request->query;
		}
		
		if (empty($query)) {
			return array();
		}
		
		$model = $this->_model;
		$this->onModel();
		
		if (empty($model)) {
			$model = $this->Controller->modelClass;
		}
		
		if (!isset($this->{$model})) {
			$this->{$model} = ClassRegistry::init($model);
		}
		
		$timezone = $this->getTimezone();
		
		$attributes = $this->{$model}->attributes();
		
		$conditions = array();
		
		foreach ($query as $condition => $value) {
			
			$attribute = $condition;
			
			$prefix_match = null;
			
			$prefixes = $this->prefixes();
			
			foreach ($prefixes as $prefix) {
				
				$match = $prefix . '-';
				$strlen = strlen($match);
				
				if (!strncmp($condition, $match, $strlen)) {
					$prefix_match = $prefix;
					$attribute = substr($condition, $strlen);
				}
				
			}
			
			$field = array_search($attribute, $field_map);

			if (empty($field)) {
				continue;
			}
			
			if (in_array($field, $this->_reserved_query_parameters)) {
				continue;
			}
			
			if (empty($prefix_match)) {
				
				if (strstr($value, '|') && strpos($value, '"')!==0) {
					$value = explode('|', $value);
				} elseif (
					(strpos($value, '"') === 0) 
					&& (strrpos($value, '"') === strlen($value)-1)
				) {
					$value = substr($value, 1, -1);
				}
				
			}
			
			$is_value_array = is_array($value);
			if (!$is_value_array) {
				$value = array($value);
			}
			
			if ($attributes[$attribute]['query'] === false) {
				continue;
			}
			
			$type = null;
			if (!empty($attributes[$attribute]['type'])) {
				$type = $attributes[$attribute]['type'];
			}
			
			if ($type === 'datetime') {
				
				foreach ($value as $value_key => $value_val) {
					
					$value[$value_key] = CakeTime::toServer($value_val, $timezone);
					
				}
				
			}
			
			$field_options = $this->{$model}->getOptions($field);
			
			if (!empty($field_options)) {
				foreach ($value as $key => $individual_value) {
					if (in_array($individual_value, $field_options)) {
						$value[$key] = array_search($individual_value, $field_options);
					}
				}
			}
			
			if (!$is_value_array) {
				$value = $value[0];
			}
			
			switch ($prefix_match) {
				
				case 'not':
					$value = $this
						->onModel($model)
						->onField($field)
						->withValue($value)
						->returnsFormattedValue();
					if ($value === 'null') {
						$conditions["{$model}.{$field} !="] = NULL;
					} else {
						$conditions[] = array(
							'OR' => array(
								"{$model}.{$field} !=" => $value,
								"{$model}.{$field}" => NULL
							)
						);
					}
					break;
				
				case 'min':
					$value = $this
						->onModel($model)
						->onField($field)
						->withValue($value)
						->returnsFormattedValue();
					$conditions["{$model}.{$field} >="] = $value; 
					break;
					
				case 'max':
					$value = $this
						->onModel($model)
						->onField($field)
						->withValue($value)
						->returnsFormattedValue();
					$conditions["{$model}.{$field} <="] = $value;
					break;
					
				case 'contains':
					if ($type === 'string') {
						$value = str_replace('%', '\\\\%', $value);
						$conditions["{$model}.{$field} LIKE"] = "%{$value}%";
					}
					break;
				
				case 'not-contains':
					if ($type === 'string') {
						$value = str_replace('%', '\\\\%', $value);
						$conditions["{$model}.{$field} NOT LIKE"] = "%{$value}%";
					}
					break;
					
				case 'starts-with':
					if ($type === 'string') {
						$value = str_replace('%', '\\\\%', $value);
						$conditions["{$model}.{$field} LIKE"] = "{$value}%";
					}
					break;
					
				case 'ends-with':
					if ($type === 'string') {
						$value = str_replace('%', '\\\\%', $value);
						$conditions["{$model}.{$field} LIKE"] = "%{$value}";
					}
					break;
					
				default:
					$value = $this
						->onModel($model)
						->onField($field)
						->withValue($value)
						->returnsFormattedValue();
						
					if (empty($value)) {
						$value = false;
					}
					
					if ($value === 'null') {
						$conditions[] = array(
							'OR' => array(
								"{$model}.{$field}" => '',
								"{$model}.{$field}" => NULL
							)
						);
						
					} else {
						$conditions["{$model}.{$field}"] = $value; 
					}
					
					break;
				
			}
			
		}
		
		return $conditions;
		
	}
	
	/**
	 * Requested Relations
	 *
	 * @since 	1.0
	 * @return 	array
	 */
	public function requestedRelations() {
		
		$related = $this->Controller->request->query('related');
		
		if (empty($related)) {
			return array();
		}
		
		if (is_string($related) && $related === 'false') {
			return false;
		}
		
		$related = explode(',', $related);
		
		return $related;
		
	}
	
	/**
	 * Requested Attributes
	 *
	 * @since 	1.0
	 * @return 	array
	 */
	public function requestedAttributes() {
		
		$attributes = $this->Controller->request->query('attributes');
		
		if (empty($attributes)) {
			return array();
		}
		
		$attributes = explode(',', $attributes);
		
		if (!empty($attributes) && !in_array('id', $attributes)) {
			$attributes = array_merge(array('id'), $attributes);
		}
		
		return $attributes;
		
	}
	
	/**
	 * Formats Value According to the Column Type
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	string
	 */
	public function returnsFormattedValue() {
		
		$model = $this->_model;
		$this->onModel();
		
		if (empty($model)) {
			$model = $this->Controller->modelClass;
		}
		
		if (!isset($this->{$model})) {
			$this->{$model} = ClassRegistry::init($model);
		}
		
		$field = $this->_field;
		$this->onField();
		
		$value = $this->_value;
		$this->withValue();
		
		if (empty($field)) {
			return $value;
		}

		$schema = $this->{$model}->schema();
		
		if (empty($schema)) {
			return $value;
		}
		
		$fields = array();
		
		foreach($schema as $columnName => $attributes) {
			if (!empty($attributes['type'])) {
				$fields[$columnName] = $attributes['type'];
			}
		}
		
		if (empty($fields[$field])) {
			return $value;
		}
		
		/**
		 * Columns types for MySQL:
		 * 
		 * primary_key, string, text, biginteger, integer, float, datetime,
		 * timestamp, time, date, binary and boolean
		 *
		 */
		if ($fields[$field] == 'datetime' && preg_match("/^\d{4}-\d{2}-\d{2}$/", $value)) {
			return $value .' 00:00:00';
		}
		
		if ($fields[$field] == 'timestamp' && preg_match("/^\d{4}-\d{2}-\d{2}$/", $value)) {
			$value = strtotime($value);
		}
		
		if ($fields[$field] == 'timestamp' 
			&& preg_match("/^\d{4}-\d{2}-\d{2} [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/", $value)
		) {
			return strtotime($value);
		}
		
		return $value;
		
	}
	
	/**
	 * Get Query Parameters from Route
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  array
	 */
	public function getParent() {
		
		if (!is_null($this->__cached_parent)) {
			return $this->__cached_parent;
		}
		
		$this->__cached_parent = array();
		
		if (!empty($this->Controller->request->params)) {
			foreach ($this->Controller->request->params as $key => $value) {
				if (substr($key, 0, 8) === 'parent__') {
					$parentModelAlias = substr($key, 8);
					$parent_model_id = $value;
				} 
			}
		}
		
		if (!empty($parentModelAlias) && !empty($parent_model_id)) {
			$this->__cached_parent = compact('parentModelAlias', 'parent_model_id');
		}
		
		return $this->__cached_parent;
		
	}
	
	/**
	 * Get Query Parameters from Route
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  array
	 */
	public function getFromRoute() {
		
		$model = $this->_model;
		$this->onModel();
		
		if (!is_null($this->__cached_route_queries)) {
			return $this->__cached_route_queries;
		}
		
		$this->__cached_route_queries = array();
		
		if (!isset($this->{$model})) {
			$this->{$model} = ClassRegistry::init($model);
		}
		
		if (!method_exists($this->{$model}, 'getAssociated')) {
			return $this->__cached_route_queries;
		}
		
		$associated = $this->{$model}->getAssociated();
		
		extract($this->getParent());
		
		if (
			!empty($parentModelAlias) && 
			!empty($parent_model_id) && 
			array_key_exists($parentModelAlias, $associated)
		) {

			$type = $associated[$parentModelAlias];

			if (
				!empty($this->Controller->request->params['modelAlias']) && 
				in_array($type, array('hasOne', 'hasMany'))
			) {
				
				$foreign_key = $this->{$model}->{$parentModelAlias}->belongsTo[$this->Controller->request->params['modelAlias']]['foreignKey'];
				$conditions = $this->{$model}->{$parentModelAlias}->belongsTo[$this->Controller->request->params['modelAlias']]['conditions'];
				
				if (empty($conditions)) {
					$conditions = array();
				}
				
				// TODO: Is there a better way to do this than using a contain?
				$filter_by_ids = $this->{$model}->{$parentModelAlias}->find('list', array(
					'fields' => array($parentModelAlias . '.' . $foreign_key),
					'conditions' => array_merge(
						array($parentModelAlias . '.' . $this->{$model}->{$parentModelAlias}->primaryKey => $parent_model_id),
						$conditions
					)
				));
						
				if (empty($filter_by_ids)) {
					$filter_by_ids = array(null); // does not exist
				}

			} elseif ($type === 'belongsTo') {
				
				$foreign_key = $this->{$model}->belongsTo[$parentModelAlias]['foreignKey'];
				$conditions = $this->{$model}->belongsTo[$parentModelAlias]['conditions'];
				
				if (empty($conditions)) {
					$conditions = array();
				}
				
				// TODO: Is there a better way to do this than using a contain?
				$filter_by_ids = $this->{$model}->find('list', array(
					'fields' => array($this->{$model}->alias . '.' . $this->{$model}->primaryKey),
					'conditions' => array_merge(
						array($model . '.' . $foreign_key => $parent_model_id),
						$conditions
					)
				));
					
				if (empty($filter_by_ids)) {
					$filter_by_ids = array(null); // does not exist
				}

			}

		}
		
		if (!empty($filter_by_ids)) {
			$filter_by_ids = array_unique($filter_by_ids);
			if (count($filter_by_ids) === 1) {
				$filter_by_ids = array_shift($filter_by_ids);
			} else {
				$filter_by_ids = implode('|', $filter_by_ids);
			}
			$this->__cached_route_queries['id'] = $filter_by_ids;
		}
		
		return $this->__cached_route_queries;
		
	}
	
	/**
	 * Get original query parameters, NOT including route parameters
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  array
	 */
	public function getWithoutRoutes() {
		
		$model = $this->_model;
		$this->onModel();
		
		$route_query = $this->onModel($model)->getFromRoute();
		
		$original_query = array_diff_assoc($this->Controller->request->query, $route_query);
		
		return $original_query;
		
	}
	
	/**
	 * Integrate Queries from Route Into Request Query
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  object
	 */
	public function integrateRouteParams() {
		
		$model = $this->_model;
		$this->onModel();
		
		$route_queries = $this
			->onModel($model)
			->getFromRoute();
		
		if (array_key_exists('id', $route_queries)) {
			if (!array_key_exists('id', $this->Controller->request->query)) {
				$this->Controller->request->query['id'] = $route_queries['id'];
			}
			$this->Controller->request->query['id'] = implode('|', array_intersect(
				explode('|', $this->Controller->request->query['id']), 
				explode('|', $route_queries['id'])
			));
		}
		
		if (
			!empty($this->Controller->request->params['id']) &&
			!empty($this->Controller->request->query['id'])
		) {
			$this->Controller->request->query['id'] = array_shift(array_intersect(
				explode('|', $this->Controller->request->query['id']),
				array($this->Controller->request->params['id'])
			));
		}
		
		return $this;
		
	}
	
	/**
	 * Parse attributes and related
	 * 
	 * Allows the user to pass requests for sub-objects as `attributes`, at which point the
	 * system parses them into `related` requests
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  object
	 */
	public function parseAttributesAndRelated() {
		
		$model = $this->_model;
		$this->onModel();
		
		$associations = $this->Controller->{$model}->getAssociated();
		$attributes = !empty($this->Controller->request->query['attributes']) ? explode(',', $this->Controller->request->query['attributes']) : array();
		$related = !empty($this->Controller->request->query['related']) ? explode(',', $this->Controller->request->query['related']) : array();
		
		if (
			empty($associations) ||
			empty($attributes) ||
			$related === array('false')
		) {
			return $this;
		}
		
		$associations = array_map('lcfirst', array_keys($associations));
		
		foreach ($attributes as $key => $attribute) {
			if (in_array($attribute, $associations)) {
				if (!in_array($attribute, $related)) {
					$related[] = $attribute;
				}
				unset($attributes[$key]);
				continue;
			}
			foreach ($associations as $association) {
				if (substr($attribute, 0, strlen($association . '.')) === $association . '.') {
					if (!in_array($association, $related)) {
						$related[] = $association;
					}
					continue(2);
				}
			}
		}
		
		$this->Controller->request->query['attributes'] = implode(',', $attributes);
		$this->Controller->request->query['related'] = implode(',', $related);
		
		return $this;
		
	}
	
	/**
	 * Get Timezone
	 *
	 * Try to validate `timezone` parameter if value is equal to `user` uses
	 * the User.timezone column value, else validate the timezone identifier 
	 * if fails to validate will return the app default timezone.	
	 *
	 * @author	Everton  Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  string
	 */
	public function getTimezone() {
		
		$default_timezone = date_default_timezone_get();
		
		if (empty($this->Controller->request->query['timezone'])) {
			return $default_timezone;
		}
		
		$timezone = $this->Controller->request->query['timezone'];
		
		if (!is_string($timezone) && !is_numeric($timezone)) {
			return $default_timezone;
		}
		
		// Set by User Preferences And Than Validate It
		if ($timezone === 'user') {
			
			$timezone = $this->Auth->user('timezone');
			
			if (empty($timezone)) {
				return $default_timezone;
			}
			
		}
		
		// Set By Timezone Identifier Abbreviation eg: JST, EST, PDT
		$identifiers = DateTimeZone::listAbbreviations();
		
		if (array_key_exists(strtolower($timezone), $identifiers)) {
			return $timezone;
		}
		
		// Set By Timezone Identifier Oslon Database format eg: America/New_York
		$identifiers = DateTimeZone::listIdentifiers();
		
		if (in_array($timezone, $identifiers)) {
			return $timezone;
		}
		
		// Set By GMT Offset eg: +09:00, 09:00, +0900, 900, +9, 9
		// Offset is assumed to be an offset without daylight saving in effect
		$timezone_offset = preg_replace('/:/', '', $timezone);
		$timezone_offset = preg_replace('/\b(?=\d{3}$)/', '0', $timezone_offset);
		$timezone_offset = preg_replace('/^(?=\d)/m', '+', $timezone_offset);
		$timezone_offset = trim($timezone_offset);
		
		if (!empty($timezone_offset) && is_numeric($timezone_offset)) {
			
			if ($timezone_offset >= -12 && $timezone_offset <= 14) {
				$timezone_offset *= 3600;
			} else {
				$timezone_offset *= 36;
			}
			
			$timezone_name = timezone_name_from_abbr('', $timezone_offset, 0);
			
			if (!empty($timezone_name)) {
				return $timezone_name;
			}
			
		}
		
		return $default_timezone;
		
	}	
	
	/**
	 * Filter Sort Params
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  object
	 */
	public function filterSortParams() {
		
		if (empty($this->Controller->request->query['sort'])) {
			return $this;
		}
		
		$sort_params = $this->Controller->request->query['sort'];
		
		if (!is_array($sort_params)) {
			$sort_params = array($sort_params);
		}
		
		$model = $this->_model;
		$this->onModel();
		
		if (empty($model)) {
			$model = $this->Controller->modelClass;
		}
		
		if (!isset($this->{$model})) {
			$this->{$model} = ClassRegistry::init($model);
		}
		
		$attributes = $this->{$model}->attributes();
		
		foreach ($sort_params as $index => $field) {
			
			$sort_params[$index] = $field = str_replace('_', '.', $field);
			
			if (empty($attributes[$field]['sort']) || $attributes[$field]['sort'] !== true) {
				unset($sort_params[$index]);
				continue;
			}
			
			if (!empty($attributes[$field]['field'])) {
				$sort_params[$index] = $field = $attributes[$field]['field'];
			}
			
		}
		
		if (empty($sort_params)) {
			unset($this->Controller->request->query['sort']);
		} else {
			$this->Controller->request->query['sort'] = array_values($sort_params);
		}
		
		return $this;
		
	}
	
	/**
	 * Prepare Query Data
	 *
	 * Used in `ApiComponent::startup()`
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  array
	 */
	public function prepare() {
		
		$model = $this->_model;
		$this->onModel();
		
		$this
			->onModel($model)
				->fixUnderscores()
			->onModel($model)
				->parseAttributesAndRelated()
			->onModel($model)
				->filterSortParams()
			->onModel($model)
				->integrateRouteParams();
		
		return $this->Controller->request->query;
		
	}
	
}
