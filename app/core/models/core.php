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

	public function setRawModel($m) { 
		$this->__model = $m; 
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

	//walidacja, a takze specyficzne operacje np dodawanie lub usuwanie pewnych pól oraz logika dodatkowa
	protected function validate(array &$m, bool $create) : ValidateResult {  return new ValidateResult(1, ''); } 

	protected function afterCreateUpdate(array &$m, bool $create) {} //wykona się, jeśli validate bedzie pomyślne

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

	protected function afterGet( &$m ) {}

	/*
		list
	*/

	protected $listConfig = [ 
		'select' => '*',
		'fromAlias' => '',
		'leftJoin' => '',
		'where' => '1',
		'orderby' => 'id DESC',
		'itemClass' => BaseModel::class,
	];

	protected function afterListItem( &$i ) {}

	public function list(array $p=[]) : array {
		$s = $p['start'] ?? 0;
		if( $s < 0 ) $s = 0;
		
		$l = $p['limit'] ?? 10;
		if( $l > 100 ) $l = 100;
		else if ( $l <= 0 ) $l = 10;

		$w = $this->listConfig['where'];
		if( strlen( trim($w) ) > 0 ) $w = 'WHERE ' . $w;

		$ord = $this->listConfig['orderby'];
		if( strlen( trim($ord) ) > 0 ) $ord = 'ORDER BY ' . $ord;

		$this->p[':select'] = 'SELECT ' . $this->listConfig['select'];
		$this->p[':leftJoin'] = $this->listConfig['leftJoin'];
		$this->p[':fromAlias'] = $this->listConfig['fromAlias'];
		$this->p[':start'] = $s;
		$this->p[':limit'] = $l;
		$this->p[':where'] = $w;		 
		$this->p[':orderby'] = $ord;		 

		$q = $this->query(':select FROM :tname :fromAlias :leftJoin :where :orderby LIMIT :start, :limit');
		$res = [];

		$itemClass = $this->listConfig['itemClass'];
		if( $q['size'] > 0 ) {
			foreach( $q['rows'] as $row ) {
				$this->afterListItem( $row ) ;
				$item = new $itemClass( $this->db );
				$item->setRawModel( $row );
				$res[] = $item;
			}
		}

		return $res;
	}

	public function select($s) {
		$this->listConfig['select'] = $s;
		return $this;
	}

	public function fromAlias($f) {
		$this->listConfig['fromAlias'] = $f;
		return $this;
	}

	public function leftJoin($l) {
		$this->listConfig['leftJoin'] = $l;
		return $this;
	}

	public function where($w) {
		$this->listConfig['where'] = $w;
		return $this;
	}

	public function orderby($o) {
		$this->listConfig['orderby'] = $o;
		return $this;
	}

	public function itemClass($ic) {
		$this->listConfig['itemClass'] = $ic;
		return $this;
	}

	public static function getIterator($db) : BaseModel { return null; }

	public function printItemsHtml(array $items) {
		echo '<br>';
		foreach( $items as $it ) {
			echo '<br>';
			print_r( $it->getModel() );
		}
	}

	public function getModels( $items ) {
		$r = [];
		foreach( $items as $item ) {
			$r[] = $item->getModel();
		}
		return $r;
	}

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