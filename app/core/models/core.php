<?php 

namespace Okdev;

//useful resources
//https://www.php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members

class BaseModel {

	protected $table_name = '';
	protected $db = null;
	protected $p = array(); //tablica z parametrami do zapytania sql
	protected $model = array('id' => 0 ); //model - dane domyslne, do inicjalizacji; id - pole obowiązkowe w każdym modelu
	protected $__model = array();//init default: __model = model

	public function __construct($db) {
		$this->_init();
		$this->db = $db;
	}

	protected function _init() {
		$this->p[':tname'] = $this->table_name;
	}

	public function getModel() { return $this->__model; }

	public function setModel($m) { 
		$this->__model = $this->model;
		$this->initDefaults( $this->__model );
		$this->readData( $m );
		return $this;
	}

	protected function readData($data) {
		$mdef = $this->model;
		foreach( $mdef as $key => $val ) {
			if( isset( $data[$key] ) ) {
				$this->__model[$key] = $data[$key];
			}
		}
	}

	protected function initDefaults(&$m) {}

	public function updateModel($m) { 
		$this->readData( $m );
		return $this;
	}

	public function resetModel() { $this->__model = []; }

	/*
		magic methods
	*/

	public function __unset($key) { unset( $this->__model[$key] ); }

	public function __isset($key) { return isset( $this->__model[$key] ); }

	public function __set($key, $val) { $this->__model[$key] = $val; }

	public function __get($key) {
		if( isset( $this->__model[$key] ) ) return $this->__model[$key];
		return null;
	}

	/*
		queries
	*/

	protected function query($sql) {
		return $this->db->query( $sql , $this->p );	
	}

	protected function queryRow($sql) {
		return $this->db->query_row( $sql , $this->p );	
	}

	protected function queryUpdate($d) {
		return $this->db->update_data( $this->table_name , $d);
	}

	protected function queryInsert($d) {
		return $this->db->insert_data( $this->table_name , $d );
	}

	//walidacja, a takze specyficzne operacje np dodawanie lub usuwanie pewnych pól oraz logika dodatkowa
	protected function validate(array &$m, bool $create) : ValidateResult {  return new ValidateResult(1, ''); } 

	protected function afterCreateUpdate(array &$m, bool $create) {} //wykona się, jeśli validate bedzie pomyślne


	/*
		create
	*/

	public function create() : ValidateResult { 
		unset( $this->__model['id'] );
		$vr = $this->validate( $this->__model, true);

		if( $vr->code > 0 ) {
			$r = $this->queryInsert( $this->__model );
			if( $r['code'] > 0 ) { 
				$vr->code = $r['id']; 
				$this->__model['id'] = (int) $r['id']; 
				$this->afterCreateUpdate( $this->__model, true );
			}
			else { $vr->code = $r['code']; $vr->msg = $r['msg']; }
		}
		
		return $vr;
	}

	/*
		update
	*/


	public function update() : ValidateResult { 
		$vr = $this->validate( $this->__model, false);

		if( $vr->code > 0 ) {
			$r = $this->queryUpdate( $this->__model );
			if( $r['code'] > 0 ) { 
				$vr->code = $r['id']; 
				$this->afterCreateUpdate( $this->__model, false );
			}
			else { $vr->code = $r['code']; $vr->msg = $r['msg']; }
		}
		
		return $vr;
	}

	/*
		get
	*/

	protected function afterGet( &$m ) {}

	public function get($id) {
		$this->p[':id'] = $id;
		$r = $this->queryRow('SELECT * FROM :tname WHERE id = :id');
		if( $r ) {
			$this->setModel( $r );
			$this->afterGet( $this->__model );
			return $this;
		}
		$this->resetModel();
		return $this;
	}

	/*
		list
	*/

	public function list() : array{}

	/*
		remove
	*/

	public function remove() {	
		$id = $this->id ?? 0;
		if( $id == 0 ) return 0;
		$this->query(" DELETE FROM :tname WHERE id = $id " );	
		$this->resetModel();
		return 1;
	}

}


class ValidateResult {
	public int $code = 0;
	public string $msg = '';

	public function __construct($code, $msg) {
		$this->code = $code;
		$this->msg = $msg;
	}
}