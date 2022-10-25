<?php 

namespace Okdev;

class BusTest {

	protected $db = null;
	public function __construct($db) { $this->db = $db; }

	public static function runAll($db) {
		$t = new BusTest($db);

		$t->test_session();
		$t->test_users();
	}

	public function test_session() {
		echo '<h3>SESSION</h3>';
		$s = new Session( $this->db );

		$token = $s->createSession( 10 );//iduser, np 10
		echo 'TOKEN = ' .$s->token . "<br>";

		$s = new Session( $this->db );
		$s->getSession( $token );
		echo 'RESTORED SESSION: ';
		print_r( $s->getModel() );
		echo '<br>';

		$s->remove();

		$s = new Session( $this->db );
		$s->getSession( $token );
		echo 'SESSION SHOULD REMOVE:';
		print_r( $s->getModel() );
		echo '<br>';

		echo '<br>';
		echo '<br>';
	}

	public function test_users() {
		echo '<h3>USERS</h3>';
		
		$u = new User( $this->db );
		$id = $u->createUser(  $_GET );
		echo 'USER CREATED, ID = ' . $id;
		echo '<br>';
		print_r( $u->getModel() );
		echo '<br>';
	}

}