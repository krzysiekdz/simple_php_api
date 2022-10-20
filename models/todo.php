<?php 

class Todo {

	protected $model = array(
		'id' => 0,
		'text' => '',
		'done' => 0,
		'weight' => 0,
		'importance' => 0,
		'urgency' => 0,
		'order' => 0,
		'date_final' => null,
		'date_todo' => null,
		'id_user' => 0,
	);	

	public function __construct() {
	}

	protected function __init_defaults(&$m) {
		// $m['date_final'] = date('Y-m-d');
		// $m['date_todo'] = date('Y-m-d');
	}

}