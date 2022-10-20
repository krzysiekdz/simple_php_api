<?php 

namespace Okdev\Models;

use Okdev\Utils;

class Account extends BaseModel {

	public function __construct($db) {
		$this->setDb( $db );
	}

	//akcja add bez parametrow : model + __init_defaults zostanie dodany do bazy (z pominięciem id)
	protected $model = array(
		'id' => 0,
		'user_name' => '',
		'email' => '',
		'password' => ''
	);	
	
	protected function __init_defaults(&$m) {
	}


	public function emailExists($s) {
		$res = $this->db->query( "SELECT id FROM t_users WHERE email = '$s' " );
		return $res['size'] > 0;
	}

	public function userNameExists($s) {
		$res = $this->db->query( "SELECT id FROM t_users WHERE user_name = '$s' " );
		return $res['size'] > 0;
	}

	public function validateUserName($s) {
		return preg_match('/[\w]{1,}[\s]*[\w]{2,}/', $s);
	}

	public function getUserByLoginData($email, $pass) {
		$enc = Utils\encode_pass( $pass );
		$res = $this->db->query( "SELECT id, user_name FROM t_users WHERE email = '$email' AND password = '$enc'" );
		return $res;
	}

}