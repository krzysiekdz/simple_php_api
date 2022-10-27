<?php 

namespace Okdev;


class User extends BaseModel {

	protected $table_name = 't_users';

	public static $adminKey = '!';

	protected $model = array(
		'id' => 0,
		'user_name' => '',
		'email' => '',
		'password' => '',
		't_type' => 0,
	);	

	
	protected function validate(array &$m, bool $create) : ValidateResult {
		if( $create || isset( $m['email'] ) ) {
			if( !validateEmail( $m['email'] ) ) return new ValidateResult(-1, "Niepoprawny adres email: ${m['email']}");
			if( $this->emailExists( $m['email'] ) ) return new ValidateResult(-1, "Taki adres e-mail już istnieje: ${m['email']}");	
		}
		
		if( $create || isset( $m['user_name'] ) ) {
			if( !$this->validateUserName( $m['user_name'] ) ) return new ValidateResult(-1, "Należy podać poprawną nazwę użytkownika (przynajmniej 3 znaki)");
			if( $this->userNameExists( $m['user_name'] ) ) return new ValidateResult(-1, "Taki użytkownik już istnieje: ${m['user_name']}");
		}

		if( $create || isset( $m['password'] ) ) {
			if( !$this->validatePass( $m['password'] ) ) return new ValidateResult(-1, "Niepoprawne hasło (przynajmniej 4 znaki).");
			$m['password'] = encode_pass( $m['password'] );	
		}

		if( $create || isset( $m['t_type'] ) ) {
			if( getParam('adminKey') != User::$adminKey ) $m['t_type'] = 0;//nie pozwalamy ustawic typu, chyba że to przekazno specjalny kod	
		}

		return new ValidateResult(1, '');
	} 

	protected function afterCreateUpdate(array &$m, bool $create) {
		unset( $m['password'] );
	} 

	public function emailExists($s) {
		$this->p['s:email'] = $s;
		$res = $this->query( "SELECT id FROM :tname WHERE email = s:email " );
		return $res['size'] > 0;
	}

	public function userNameExists($s) {
		$this->p['s:user_name'] = $s;
		$res = $this->query( "SELECT id FROM :tname WHERE user_name = s:user_name " );
		return $res['size'] > 0;
	}

	public static function validateUserName($s) {
		return preg_match('/[\w]{1,}[\s]*[\w]{2,}/', $s);
	}

	public static function validatePass($s) {
		return preg_match('/^.{4,}$/', $s);
	}

	public function getUser($email, $pass) {
		$this->p['s:email'] = $email;
		$this->p['s:password'] = encode_pass( $pass );
		$r = $this->queryRow( "SELECT * FROM :tname WHERE email = s:email AND password = s:password " );

		if( $r ) {
			$this->setModel( $r );
			$this->afterGet( $this->__model );
			return $this;
		}
		$this->resetModel();
		return $this;
	}

	protected function afterGet( &$m ) {
		unset( $m['password'] );
	}

}