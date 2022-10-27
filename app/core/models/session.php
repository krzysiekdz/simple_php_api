<?php 

namespace Okdev;


class Session extends BaseModel {

	protected $table_name = 't_sessions';
	protected $session_timeout = 60 * 60; // 1h - w sekundach

	protected $model = array(
		'id' => 0,
		'id_user' => 0,
		'token' => '',
		'date_start' => '',
		'date_last' => '',
	);	
	
	
	//tworzenie sesji jest wewnętrzne, tzn nie jest na podstawie parametrow GET/POST
	public function createSession($iduser) {
		
		$limit = 100; //limit prób : 100 powinno wystarczyć 
		$i = $iduser;
		do { //generowanie unikalnego tokenu
			$token = create_token( $i++ );
			$res = $this->query("SELECT id FROM :tname WHERE token = '$token' ");	
		} while( $res['size'] > 0 && ( ($limit--) > 0 ) );

		
		$this->query("  DELETE FROM :tname WHERE id_user = $iduser "); //uzytkownik moze byc zalogowany tylko raz - usuniecie sesji 

		$s = array('id_user' => $iduser, 'token' => $token);
		$s['date_start'] = date_now();
		$s['date_last'] = date_now();

		$r = $this->queryInsert( $s );
		if( $r['code'] > 0 ) {
			$s['id'] = $r['id'];
			$this->setModel( $s );
		}
		
		return $token;
	}


	public function getSession($token) {
		$this->setModel([]);
		if( !$token ) return array( 'code'=> -1, 'msg'=> 'Należy podać identyfikator sesji' );

		$r = $this->query(" SELECT * FROM :tname WHERE token = '$token' ");
		if( $r['size'] == 0 ) return array( 'code' => -1, 'msg'=> 'Musisz być zalogowany' );
		$r = $r['row'];

		if( $this->session_timeout > (time() - strtotime( $r['date_last'] )) )  { //sesja ważna
			$s = array( 'date_last' => date_now(), 'id' => $r['id'] );
			$this->queryUpdate( $s );

			$s = $this->queryRow(" SELECT * FROM :tname WHERE token = '$token' ");
			$this->setModel( $s );

			return array( 'code'=>1 );
		} 
		else { //sesja nieważna
			$this->query(" DELETE FROM :tname WHERE token = '$token' ");

			return array( 'code' => -1, 'msg'=> 'Twoja sesja wygasła' );
		}
	}

}