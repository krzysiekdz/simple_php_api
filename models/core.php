<?php 

namespace Okdev\Models;

use Okdev\Utils;

class BaseModel {

	public function __construct() {}


	//dane default do modelu - jest niezmienialny
	protected $model = array(
		'id' => 0
	);	

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
		$this->__read_data($this->__model, 'Okdev\Utils\getParam');
	}

	protected function __read_data(&$m, $p) {
		$mdef = $this->model;
		foreach( $mdef as $key => $val ) {
			if( Utils\isParam($key) ) {
				$m[$key] = $p($key, $val);//$val jest tutaj niepotrzebne, bo i tak nie zostanie zwrocone 
			}
		}
	}

	public function init_defaults() {
		$this->__model = $this->model;
		$this->__init_defaults($this->__model);
	}

	protected function __init_defaults(&$m) {}



}