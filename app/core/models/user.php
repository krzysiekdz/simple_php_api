<?php 

namespace Okdev;


class User extends BaseModel {

	protected $table_name = 't_users';

	protected $model = array(
		'id' => 0,
		'user_name' => '',
		'email' => '',
		'password' => '',
		't_type' => 0,
	);	
	
	protected function __init_defaults(&$m) {
	}

	//przemyslec, czy walidacje robic w modelu czy w kontrolerze
	protected function __validate_create(&$m) : bool {
		unset( $m['id'] );
	} 


	public function emailExists($s) {
		$this->def['s:email'] = $s;
		$res = $this->query( "SELECT id FROM :tname WHERE email = s:email " );
		return $res['size'] > 0;
	}

	public function userNameExists($s) {
		$this->def['s:user_name'] = $s;
		$res = $this->query( "SELECT id FROM :tname WHERE user_name = s:user_name " );
		return $res['size'] > 0;
	}

	public function validateUserName($s) {
		return preg_match('/[\w]{1,}[\s]*[\w]{2,}/', $s);
	}

	public function getUser($email, $pass) {
		$enc = encode_pass( $pass );
		$r = $this->query_row( "SELECT * FROM :tname WHERE email = '$email' AND password = '$enc'" );
		if( $r ) {
			unset( $r['password'] );
			$this->setModel( $r );
			return true;
		}
		return false;
	}

}