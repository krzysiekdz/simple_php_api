<?php 

namespace Okdev;


class BaseModel {

	public function __construct() {}


	//dane default do modelu - jest niezmienialny
	protected $model = array(
		'id' => 0
	);	

	protected $validators = null; //tutaj moze byc tablica funkcji z walidatorami - na potem

	protected $db = null;

	public function setDb($db) {
		$this->db = $db;
	}

	//tutaj przechowywane dane modelu
	protected $__model = array();

	public function getModel() { return $this->__model; }

	public function setModel($m) { $this->__model = $m; }

	public function removeFromModel($key) { unset( $this->__model[$key] ); }

	public function read_data() {
		$this->__read_data();
	}

	protected function __read_data() {
		$mdef = $this->model;
		foreach( $mdef as $key => $val ) {
			if( isParam($key) ) {
				$this->__model[$key] = getParam($key, $val);//$val jest tutaj niepotrzebne, bo i tak nie zostanie zwrocone; przydalaby sie tutaj walidacja
			}
		}
	}

	public function init_defaults() {
		$this->__model = $this->model;
		$this->__init_defaults($this->__model);
	}

	protected function __init_defaults(&$m) {}



}