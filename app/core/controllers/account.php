<?php 

namespace Okdev;


class AccountController extends BaseController {

	// protected $def_list = false;

	//listowanie uzytkownikow - tylko admin - todo
	protected function createIterator() { return User::getIterator( $this->db ); }

	protected function createModel() { return new User( $this->db ); }

	public function action_login() {
		echo 'login';
	}
}