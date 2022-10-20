<?php 

namespace Okdev\Models;


class Note extends BaseModel {

	//jesli zrobimy add bez parametrow, to taki obiekt (+ __init_defaults) zostanie dodany do bazy (z pominięciem id)
	protected $model = array(
		'id' => 0,
		'title' => '',
		'text' => '',
		'id_user' => 0,
		'c_order' => 0,
		'date_add' => null,
		'date_modif' => null,
	);	



	public function __construct() {
	}

	protected function __init_defaults(&$m) {
		$m['date_add'] = date('Y-m-d h-i-s');//dodac tutaj potem godzine
		$m['date_modif'] = date('Y-m-d h-i-s');
	}

}