<?php 

namespace Okdev;


class BaseController {

	protected $base = ''; //adres bazowy kontrolera, np account w adresie : account/login
	protected $route = null;//część adresu url która aktywuje akcję kontrolera, np login w adresie: account/login
	protected $db = null;
	protected $model = null;
	protected $session = null;
	protected $result = array();//tablica wartosci zwracana z api - jeśli api restowe 

	protected $def_list = true;
	protected $def_get = true;
	protected $def_create = true;
	protected $def_update = true;
	protected $def_remove = true;
	
	protected function createModel() { return null; }

	protected function createIterator() { return null; }

	public  function __construct($base) { $this->base = $base; }

	public function run() {
		if( count($this->route) > 0 ) {
			$r =  explode('?', $this->route[0]);
			$r = $r[0];
			$action = 'action_' . $r; //można wywołac tylko metody action_... - inne są ukryte
			if ( method_exists( $this, $action ) ) {
				$this->{$action}();
			}
			else {
				$this->notFound();		
			}
		}
		else {
			$this->action_index();
		}

		$debug = getParam('debug');
		if( $debug == '999' ) {
			$this->result['__db_queries'] = $this->db->log;
		}
	}

	public function setRoute($url, $i) {
		$this->route = [];

		$r = explode('/', $url);
		if( count($r) == 0 ) {
			$this->route[] = '';
			return ; 
		}
		if( $r[0] == '' ) array_shift($r);
		
		$this->route = array_copy( $r, $i );
	}

	public function setRouteArr($r, $i) {
		$this->route = array_copy( $r, $i );
	}

	public function setDb($db = null) {
		if(!$db) {
			$this->db = Framework::$db;	
		}
	}

	public function callController($c) {
		$c->setDb();
		//przekazujemy kolejny route, np jesli jestesmy na : home/news/list to route=news/list a przekazujemy : list
		$c->setRouteArr( $this->route, 1 ); 
		$c->run();
		$this->result = $c->result;
	}

	public function resultJson() {
		ret_json( $this->result );
	}

	protected function action_index() {
		$this->result = [ 'code' => 1, 'msg' => 'index' ];
	}

	protected function notFound($msg = '', $code = -404) {
		if($msg == '') $msg = 'Nie znaleziono zasobu';
		$this->result =  array( 'code'=> $code, 'msg'=>$msg );
	}

	protected function routeNotFound($r) {
		$msg = 'Nie znaleziono routingu dla: ' . $r ;
		$this->result = array( 'code'=>-404, 'msg'=>$msg ) ;
	}

	protected function resultErr($msg = '', $code = -1 ) {
		if($msg == '') $msg = 'Wystąpił błąd';
		$this->result =  array( 'code'=> $code, 'msg'=>$msg ) ;
	}

	protected function setResult($data, $code = 0) {
		foreach($data as $k=>$v) {
			$this->result[$k] = $v;	
		}
		if( $code ) {
			$this->result['code'] = $code;
		}
	}

	protected function resultCode($code) {
		$this->result['code'] = $code;
	}

	// protected function checkSession() {
	// 	$token = getParam('token');
	// 	$this->session = new Session( $this->db );
		
	// 	$res = $this->session->checkSession( $token );
	// 	$this->setResult( $res, $res['code'] );

	// 	return $res['code'] > 0;
	// }
	

	/*
	* action_list
	*/

	//dodac mozliwosc okreslenia dostepu: dla zalogowanych badz dla wszytkich
	protected function action_list() {
		if( !$this->def_list ) return $this->notFound();

		$start = getParamInt('start');
		$limit = getParamInt('limit');

		$iter = $this->createIterator();
		if( !$iter ) return $this->notFound('list - no iterator');

		$items = $iter->list( ['start' => $start, 'limit' => $limit] );

		$this->result['rows'] = $iter->getModels( $items );
		$this->resultCode(1);
	}


	/*
	* action_get
	*/

	protected function action_get() {

		if( !$this->def_get ) return $this->notFound();

		$id = getParamInt('id');

		$model = $this->createModel();
		if( !$model ) return $this->notFound('get - no model');

		$model->get( $id );

		if( $model->id ) {
			$this->result['row'] = $model->getModel();
			$this->resultCode(1);	
		}
		else {
			$this->notFound('Not found record: ' . $id)	;
		}
		
	}



	/*
	* action_add
	*/


	// protected function action_add() {
	// 	if( $this->prevent_default_add ) return ret_err( 'Forbidden!', -400 );
	// 	$this->__add_init();
	// 	$this->__add_finalize();
	// }

	// protected function __add_init() {
	// 	$this->model = $this->createModel();
	// 	if( $this->model == null ) return;
	// 	$m = $this->model;

	// 	$m->init_defaults();
	// 	$m->read_data();
	// 	$m->removeFromModel('id');
	// }

	// protected function __add_finalize() {
	// 	$res = $this->db->insert_data( $this->table_name, $this->model->getModel());
	// 	if($res['code'] > 0) {
	// 		$r = $this->db->get_by_id( $this->table_name, $res['id'] );
	// 		if($r['code'] > 0) $res['row'] = $r['row'];
	// 	}
	// 	ret_json($res);
	// }



	/*
	* action_edit
	*/


	// protected function action_edit() {
	// 	if( $this->prevent_default_edit ) return ret_err( 'Forbidden!', -400 );
	// 	$this->__edit_init();
	// 	$this->__edit_finalize();
	// }

	// protected function __edit_init() {
	// 	$this->model = $this->createModel();
	// 	if( $this->model == null ) return;
	// 	$m = $this->model;

	// 	$m->read_data();
	// }

	// protected function __edit_finalize() {
	// 	$m = $this->model->getModel(); 
	// 	if(!isset($m['id'])  || $m['id'] <= 0) {
	// 		ret_not_found('Missing id parameter!');
	// 		return;
	// 	}

	// 	$res = $this->db->update_data( $this->table_name, $this->model->getModel());
	// 	if($res['code'] > 0) {
	// 		$r = $this->db->get_by_id( $this->table_name, $res['id'] );
	// 		if($r['code'] > 0) $res['row'] = $r['row'];

	// 		ret_json($res);
	// 	}
	// 	else {
	// 		ret_not_found('Not found item!');
	// 	}
		
	// }


}
