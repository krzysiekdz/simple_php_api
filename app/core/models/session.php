<?php 

namespace Okdev;


class Session extends BaseModel {

	protected $session_timeout = 60 * 60; // 1h - w sekundach
	protected $table_name = 't_sessions';
	protected $table_name_users = 't_users';
	public $session = null;
	public $user = null;

	protected $def = array();

	public function __construct($db) {
		$this->setDb( $db );
		$this->def[':t'] = $this->table_name;
		$this->def[':tu'] = $this->table_name_users;
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
			$token = create_token( $i++ );
			$res = $this->db->query("SELECT id FROM :t WHERE token = '$token' ", $this->def );	
		} while( $res['size'] > 0 && ( ($limit--) > 0 ) );

		
		$this->db->query("  DELETE FROM :t WHERE id_user = $iduser ", $this->def); //uzytkownik moze byc zalogowany tylko raz - usuniecie sesji 

		$s = array('id_user' => $iduser, 'token' => $token);
		$s['date_start'] = date_now();
		$s['date_last'] = date_now();
		$this->db->insert_data( $this->table_name , $s );

		return $token;
	}


	public function checkSession($token) {
		
		if( !$token ) return array( 'code'=> -1, 'msg'=> 'Należy podać identyfikator sesji' );

		$r = $this->db->query(" SELECT * FROM :t WHERE token = '$token' ", $this->def );
		if( $r['size'] == 0 ) return array( 'code' => -1, 'msg'=> 'Twoja sesja wygasła' );
		$r = $r['row'];

		if( $this->session_timeout > (time() - strtotime( $r['date_last'] )) )  { //sesja ważna
			$s = array( 'date_last' => date_now(), 'id' => $r['id'] );
			$this->db->update_data( $this->table_name , $s);

			$this->session = $this->db->query_row(" SELECT * FROM :t WHERE token = '$token' ", $this->def );
			$this->user = $this->db->query_row(" SELECT * FROM :tu WHERE id = :uid ", 
				array( ':uid '=> $this->session['id_user'], ':tu' => $this->table_name_users )  );

			return array( 'code'=>1 );
		} 
		else { //sesja nieważna
			$this->db->query(" DELETE FROM :t WHERE token = '$token' ", $this->def );

			return array( 'code' => -1, 'msg'=> 'Twoja sesja wygasła' );
		}
	}

	public function removeSession($id) {
		$this->db->query(" DELETE FROM :t WHERE id = $id ", $this->def );		
	}

}