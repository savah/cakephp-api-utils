<?php
App::uses('Model', 'Model');

class AppModel extends Model {

	public $actsAs = array(
		'Containable',
		'Api.Api' // Contains methods required for components to do their jobs
	);

	public function beforeSave($options = array()) {

		$fields = $this->schema();

		if (!empty($fields['user_id']) && empty($this->data[$this->alias]['user_id'])) {
			$this->data[$this->alias]['user_id'] = $this->userId();
			if (!empty($this->whitelist) && !in_array('user_id', $this->whitelist)) {
				$this->whitelist[] = 'user_id';
			}
		}

	}

}