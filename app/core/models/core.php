<?php 

namespace Okdev;

//useful resources
//https://www.php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members

class BaseModel {

	protected $table_name = '';
	protected $db = null;
	protected $def = array(); //tablica z parametrami do zapytania sql

	public function __construct($db) {
		$this->__init();
		$this->db = $db;
	}

	protected function __init() {
		$this->def[':tname'] = $this->table_name;
	}

	//dane default do modelu - jest niezmienialny
	protected $model = array(
		'id' => 0
	);	

	//tutaj przechowywane dane modelu
	protected $__model = array();

	public function getModel() { return $this->__model; }

	public function setModel($m) { $this->__model = $m; }

	public function __unset($key) { unset( $this->__model[$key] ); }

	public function __isset($key) { return isset( $this->__model[$key] ); }

	public function __set($key, $val) { $this->__model[$key] = $val; }

	public function __get($key) {
		if( isset( $this->__model[$key] ) ) return $this->__model[$key];
		return null;
	}

	protected function query($sql) {
		return $this->db->query( $sql , $this->def );	
	}

	protected function query_row($sql) {
		return $this->db->query_row( $sql , $this->def );	
	}

	protected function query_update($d) {
		return $this->db->update_data( $this->table_name , $d);
	}

	protected function query_insert($d) {
		return $this->db->insert_data( $this->table_name , $d );
	}

	protected function read_data($data) {
		$mdef = $this->model;
		foreach( $mdef as $key => $val ) {
			if( isset( $data[$key] ) ) {
				$this->__model[$key] = $data[$key];
			}
		}
	}

	protected function __init_defaults(&$m) {}

	//specyficzne operacje dla create, np dodawanie lub usuwanie pewnych pól oraz logika dodatkowa
	protected function __validate_create(&$m) : bool {} 

	public function create( $data ) { //data == POST data
		$this->__model = $this->model;
		$this->__init_defaults( $this->__model );
		$this->read_data( $data );
		if( $this->__validate_create( $this->__model ) ) {
			$r = $this->query_insert( $this->__model );
			if( $r['code'] > 0 ) return $r['id'];
		}
		
		return 0;
	}

	public function remove() {	
		$id = $this->id ?? 0;
		if( $id == 0 ) return 0;
		$this->query(" DELETE FROM :tname WHERE id = $id " );	
		$this->setModel([]);
		return 1;
	}

}