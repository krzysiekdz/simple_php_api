<?php 

namespace Okdev\Models;

use Okdev\Utils;

class Session extends BaseModel {

	protected $session_timeout = 60 * 60; // 1h - w sekundach
	public $session = null;
	public $user = null;

	public function __construct($db) {
		$this->setDb( $db );
	}

	//akcja add bez parametrow : model + __init_defaults zostanie dodany do bazy (z pominięciem id)
	protected $model = array(
		'id' => 0,
		'id_user' => '',
		'token' => '',
		'date_start' => '',
		'date_last' => '',
	);	
	
	protected function __init_defaults(&$m) {
	}


	public function createSession($iduser) {
		
		$limit = 100; //limit prób - 100 powinno wystarczyć 
		$i = $iduser;
		do { //generowanie unikalnego tokenu
			$token = Utils\create_token( $i++ );
			$res = $this->db->query("SELECT id FROM t_session WHERE token = '$token' ");	
		} while( $res['size'] > 0 && ( ($limit--) > 0 ) );

		
		$this->db->query("  DELETE FROM t_session WHERE id_user = $iduser "); //uzytkownik moze byc zalogowany tylko raz - usuniecie sesji 

		$s = array('id_user' => $iduser, 'token' => $token);
		$s['date_start'] = Utils\date_now();
		$s['date_last'] = Utils\date_now();
		$this->db->insert_data( 't_session' , $s );

		return $token;
	}


	public function checkSession($token) {
		
		if( !$token ) return array( 'code'=> -1, 'msg'=> 'Należy podać identyfikator sesji' );

		$r = $this->db->query(" SELECT * FROM t_session WHERE token = '$token' ");
		if( $r['size'] == 0 ) return array( 'code' => -1, 'msg'=> 'Twoja sesja wygasła' );
		$r = $r['row'];

		if( $this->session_timeout > (time() - strtotime( $r['date_last'] )) )  { //sesja ważna
			$s = array( 'date_last' => Utils\date_now(), 'id' => $r['id'] );
			$this->db->update_data( 't_session' , $s);

			$this->session = $this->db->query_row(" SELECT id, id_user FROM t_session WHERE token = '$token' ");
			$this->user = $this->db->query_row(" SELECT id, user_name, email, password FROM t_users WHERE id = :uid ", 
				array( ':uid '=> $this->session['id_user']));

			return array( 'code'=>1 );
		} 
		else { //sesja nieważna
			$this->db->query(" DELETE FROM t_session WHERE token = '$token' ");

			return array( 'code' => -1, 'msg'=> 'Twoja sesja wygasła' );
		}
	}

	public function removeSession($id) {
		$this->db->query(" DELETE FROM t_session WHERE id = $id ");		
	}

}