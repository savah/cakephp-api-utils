<?php
App::uses('PaginatorComponent', 'Controller' . DS . 'Component');

/**
 * Api Paginator Component
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
class ApiPaginatorComponent extends PaginatorComponent {
	
	/**
	 * Constructor
	 *
	 * @since   1.0
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @param   ComponentCollection $collection A ComponentCollection this component can use to lazy load its components
	 * @param   array $settings Array of configuration settings.
	 * @return  void
	 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		
		$settings = array_merge($this->settings, (array)$settings);
		
		$this->Controller = $collection->getController();
		
		parent::__construct($collection, $settings);
	
	}
		
	/**
	 * Validate that the desired sorting can be performed on the $object. Only fields or
	 * virtualFields can be sorted on. The direction param will also be sanitized. Lastly
	 * sort + direction keys will be converted into the model friendly order key.
	 *
	 * You can use the whitelist parameter to control which columns/fields are available for sorting.
	 * This helps prevent users from ordering large result sets on un-indexed values.
	 *
	 * @since   1.0
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @param   Model $object The model being paginated.
	 * @param   array $options The pagination options being used for this request.
	 * @param   array $whitelist The list of columns that can be used for sorting. If empty all keys are allowed.
	 * @return  array An array of options with sort + direction removed and replaced with order if possible.
	 */
	public function validateSort(Model $object, array $options, array $whitelist = array()) {
		
		$options = $this->multipleSort($object, $options, $whitelist);
		
		$options = $this->defaultOrder($object, $options, $whitelist);
		
		return parent::validateSort($object, $options, $whitelist);
		
	}
	
	/**
	 * Multiple Sort
	 *
	 * @since   1.0
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @param   Model $object The model being paginated.
	 * @param   array $options The pagination options being used for this request.
	 * @param   array $whitelist The list of columns that can be used for sorting. If empty all keys are allowed.
	 * @return  array An array of options with sort + direction removed and replaced with order if possible.
	 */
	public function multipleSort(Model $object, array $options, array $whitelist = array()) {
		
		if (empty($options['sort'])) {
			return $options;
		}
		
		if (!is_array($options['sort'])) {
			$sort_fields = array($options['sort']);
		} else {
			$sort_fields = array_values($options['sort']);
		}
		
		if (empty($options['direction'])) {
			$directions = array('asc');
		} elseif (!is_array($options['direction'])) {
			$directions = array($options['direction']);
		} else {
			$directions = array_values($options['direction']);
		}
		
		foreach ($sort_fields as $index => $field) {
			
			if (empty($directions[$index])) {
				$direction = $directions[0];
			} else {
				$direction = $directions[$index];
			}
			
			$options['order'][$field] = $direction;
			
		}
				
		unset($options['sort']);
		unset($options['direction']);
		
		return $options;
		
	}
	
	/**
	 * Default Order
	 *
	 * Applies default order using `$object->primaryKey`
	 *
	 * @since   1.0
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @param   Model $object The model being paginated.
	 * @param   array $options The pagination options being used for this request.
	 * @param   array $whitelist The list of columns that can be used for sorting. If empty all keys are allowed.
	 * @return  array An array of options with sort + direction removed and replaced with order if possible.
	 */
	public function defaultOrder(Model $object, array $options, array $whitelist = array()) {
		
		if (!empty($options['order'])) {
			return $options;
		}
		
		if (!empty($object->order)) {
			return $options;
		}
		
		$options['order'][$object->primaryKey] = 'asc';
		
		return $options;
		
	}
	
	/**
	 * Merges the various options that Pagination uses.
	 * Pulls settings together from the following places:
	 *
	 * - General pagination settings
	 * - Model specific settings.
	 * - Request parameters
	 *
	 * The result of this method is the aggregate of all the option sets combined together. You can change
	 * PaginatorComponent::$whitelist to modify which options/values can be set using request parameters.
	 *
	 * THIS HAS BEEN MODIFIED: if `$default['parentModel']` is set, limit is NOT set to the querystring (if aplicable)
	 * 
	 * @since   1.0
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @author  CakePHP Core Dev Team
	 * @param   string $alias Model alias being paginated, if the general settings has a key with this value
	 *   that key's settings will be used for pagination instead of the general ones.
	 * @return array Array of merged options.
	 */
	public function mergeOptions($alias) {
		
		$defaults = $this->getDefaults($alias);
		
		if (empty($defaults['parentModel'])) {
			return parent::mergeOptions($alias);
		}
		
		return $defaults;
		
	}
	
}
