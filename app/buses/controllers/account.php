<?php 

namespace Okdev\Controllers;

include_once $_SERVER['DOCUMENT_ROOT'].'/models/account.php';

use Okdev\Utils;
use Okdev\Models;

class Account extends BaseController {
	
	public static $url = 'account';

	protected $table_name = 't_users';

	protected function createModel() { return new Models\Account( $this->db ); }


	public  function __construct($r, $i) {
		parent::__construct($r, $i, self::$url);
	}


	public function action_register() {
		$email = Utils\getParam('email');  
		$pass = Utils\getParam('password');
		$user_name = Utils\getParam('user_name');

		$model = $this->createModel();

		if( !Utils\validateEmail($email) ) {
			return  $this->resultErr( 'Niepoprawny adres email' );
		}
		else if( $model->emailExists( $email ) ) {
			return  $this->resultErr( 'Taki adres e-mail już istnieje.' );	
		}
		else if( !$model->validateUserName( $user_name ) ) {
			return  $this->resultErr( 'Należy podać nazwę użytkownika (przynajmniej 3 znaki)' );	
		}
		else if( $model->userNameExists( $user_name ) ) {
			return  $this->resultErr( 'Taki użytkownik już istnieje.' );	
		}
		else if ( !Utils\validatePass($pass) ) {
			return  $this->resultErr( 'Niepoprawne hasło (przynajmniej 4 znaki).' );	
		}
		
		$model->read_data();
		$d = $model->getModel();
		$d['password'] = Utils\encode_pass( $d['password'] );
		$this->result = $this->db->insert_data( $this->table_name,  $d );
	}


	public function action_login() {
		$email = Utils\getParam('email');  
		$pass = Utils\getParam('password');

		$model = $this->createModel();
		$sess = new Models\Session( $this->db );

		if( !Utils\validateEmail($email) ) {
			return $this->resultErr( 'Niepoprawny adres email.' );
		}
		else if ( !$pass ) {
			return $this->resultErr( 'Podaj hasło.' );	
		}
		
		$res = $model->getUserByLoginData( $email, $pass );
		if( $res['size'] > 0 ) { //poprawne dane : tworzenie sesji
			$d = $res['row'];

			$token = $sess->createSession( $d['id'] );

			return $this->setResult( array('user_name'=>$d['user_name'],  'token'=>$token ) );
		}
		else {
			return $this->resultErr( 'Niepoprawny login lub hasło.' );
		}
	}

	public function action_logout() {
		if( ! $this->checkSession() ) return;

		$ids = $this->session->session['id'];
		$this->session->removeSession( $ids );
	}


	public function action_get_user() {
		if( ! $this->checkSession() ) return;

		$this->setResult( array( 'user'=> $this->session->user ) );
	}
	
}