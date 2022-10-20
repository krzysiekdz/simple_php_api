<?php 

namespace Okdev\Controllers;

include_once $_SERVER['DOCUMENT_ROOT'].'/models/session.php';

use Okdev;
use Okdev\Utils;
use Okdev\Models;

class BaseController {
	protected $route = null;
	protected $db = null;
	protected $model = null;
	protected $table_name = '';

	//konfiguracja dla list
	protected $def_limit = 10;
	protected $max_limit = 100;
	protected $start = 0;
	protected $limit = 0;

	protected $prevent_default_list = true;
	protected $prevent_default_get = true;
	protected $prevent_default_add = true;
	protected $prevent_default_edit = true;

	protected $session = null;
	
	protected $result = array();
	
	protected function createModel() { return null; }


	//każdy kontroler wykonuje ten kod - rozpoznanie routingu i wywołanie odpowiedniej akcji
	public  function __construct($r, $i, $url) {
		$this->db = Okdev\Framework::$db;

		$this->route = Utils\array_copy($r, $i);

		if( count($this->route) > 0 ) {
			$r0 =  explode('?', $this->route[0]);
			$r0 = $r0[0];
			$action = 'action_' . $r0; //można wywołac tylko metody action_... - inne są ukryte
			if ( method_exists( $this, $action ) ) {
				$this->{$action}();
			}
			else {
				$this->result = Utils\route_not_found($url);		
			}
		}
		else {
			$this->result = Utils\route_not_found($url);		
		}

		$debug = Utils\getParam('debug');
		if( $debug == '999' ) {
			$this->result['__db_queries__'] = $this->db->log;
		}

		Utils\ret_json( $this->result );
	}


	protected function checkSession() {
		$token = Utils\getParam('token');
		$this->session = new Models\Session( $this->db );
		
		$res = $this->session->checkSession( $token );
		$this->setResult( $res, $res['code'] );

		return $res['code'] > 0;
	}

	protected function resultErr($msg = '', $code = -1 ) {
		$this->result = Utils\ret_err( $msg, $code );	
	}

	protected function setResult($data, $code = 1 ) {
		foreach($data as $k=>$v) {
			$this->result[$k] = $v;	
		}
		$this->result['code'] = $code;
	}

	protected function resultCode($code) {
		$this->result['code'] = $code;
	}



	protected function parseListParams() {
		$this->start = Utils\getParamInt('start');
		$this->limit = Utils\getParamInt('limit', $this->def_limit);
		

		$this->start = Utils\assert_int_pos( $this->start );
		$this->limit = Utils\assert_int_max( $this->limit, 1, $this->max_limit );
	}

	/*
	* action_list
	*/

	protected function action_list() {
		if( $this->prevent_default_list ) return Utils\ret_err( 'Forbidden!', -400 );
		$this->parseListParams();
		$res = $this->db->list_data(  $this->table_name , $this->start , $this->limit );
		Utils\ret_json( $res );
	}


	/*
	* action_get
	*/

	protected function action_get() {
		if( $this->prevent_default_get ) return Utils\ret_err( 'Forbidden!', -400 );
		$id = Utils\getParam( 'id', 0 );
		if( $id > 0 ) {
			$res = $this->db->get_by_id(  $this->table_name , $id );
			if($res['code'] > 0) {
				Utils\ret_json( $res );	
			}
			else {
				Utils\ret_not_found( 'Not found!' );		
			}
			
		}
		else {
			Utils\ret_not_found( 'Wrong id parameter!' );	
		}
	}



	/*
	* action_add
	*/


	protected function action_add() {
		if( $this->prevent_default_add ) return Utils\ret_err( 'Forbidden!', -400 );
		$this->__add_init();
		$this->__add_finalize();
	}

	protected function __add_init() {
		$this->model = $this->createModel();
		if( $this->model == null ) return;
		$m = $this->model;

		$m->init_defaults();
		$m->read_data();
		$m->removeFromModel('id');
	}

	protected function __add_finalize() {
		$res = $this->db->insert_data( $this->table_name, $this->model->getModel());
		if($res['code'] > 0) {
			$r = $this->db->get_by_id( $this->table_name, $res['id'] );
			if($r['code'] > 0) $res['row'] = $r['row'];
		}
		Utils\ret_json($res);
	}



	/*
	* action_edit
	*/


	protected function action_edit() {
		if( $this->prevent_default_edit ) return Utils\ret_err( 'Forbidden!', -400 );
		$this->__edit_init();
		$this->__edit_finalize();
	}

	protected function __edit_init() {
		$this->model = $this->createModel();
		if( $this->model == null ) return;
		$m = $this->model;

		$m->read_data();
	}

	protected function __edit_finalize() {
		$m = $this->model->getModel(); 
		if(!isset($m['id'])  || $m['id'] <= 0) {
			Utils\ret_not_found('Missing id parameter!');
			return;
		}

		$res = $this->db->update_data( $this->table_name, $this->model->getModel());
		if($res['code'] > 0) {
			$r = $this->db->get_by_id( $this->table_name, $res['id'] );
			if($r['code'] > 0) $res['row'] = $r['row'];

			Utils\ret_json($res);
		}
		else {
			Utils\ret_not_found('Not found item!');
		}
		
	}


}
