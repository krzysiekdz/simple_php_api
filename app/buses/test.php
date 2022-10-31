<?php 

namespace Okdev;


//https://okdev.hmcloud.pl/buses/home/news/list?debug=999&id=1
class BusTest {

	protected $db = null;
	public function __construct($db) { $this->db = $db; }

	public static function runAll($db) {
		$t = new BusTest($db);

		// $t->test_session();
		// $t->test_users();
		$t->test_home_news();

		echo '<h3>Logs:</h3>';
		$db->printLogsHtml();
	}


	public function test_home_news() {
		echo '<h3>HOME NEWS</h3>';
		
		echo 'NEWS LIST:';
		$iter = HomeNews::getIterator( $this->db );
		$items = $iter->list();
		$iter->printItemsHtml( $items );
		echo '<br>';

		$news = create(new HomeNews($this->db))->get(3);
		echo 'GET id = 3:';
		echo '<br>';
		print_r( $news->getModel() );		
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

	//zaczac od zrobienia list(), potem kontroler account
	public function test_users() {
		echo '<h3>USERS</h3>';
		
		echo 'USER CREATION:';
		echo '<br>';
		$d=[ 'email'=>'abc@b.pl', 'user_name'=>'abc', 'password'=>'1234' ];
		$u = create(new User( $this->db ))->setModel( $d );
		echo 'before create: ';
		echo '<br>';
		print_r( $u->getModel() );
		$vr = $u->create();
		echo '<br>';
		echo 'after create: ';
		echo '<br>';
		print_r($vr);
		echo '<br>';
		print_r( $u->getModel() );
		echo '<br>';
		echo '<br>';


		$d2=[ 'email'=>'abc2@b.pl', 'user_name'=>'abc', 'password'=>'1234' ];
		$u2 = create(new User( $this->db ))->setModel( $d2 );
		$vr = $u2->create();
		echo 'USER CREATION WITH THE SAME USER_NAME:';
		echo '<br>';
		print_r($vr);
		echo '<br>';
		print_r( $u2->getModel() );
		echo '<br>';
		echo '<br>';

		$u3 = create(new User( $this->db ))->getUser( 'abc@b.pl', '1234' );
		echo 'USER GET WITH EMAIL AND PASSWORD:';
		echo '<br>';
		print_r( $u3->getModel() );
		echo '<br>';
		echo '<br>';

		$u3->getUser( 'abc@b.pl', '12345' );
		echo 'USER GET WITH WRONG PASSWORD:';
		echo '<br>';
		print_r( $u3->getModel() );
		echo '<br>';
		echo '<br>';

		$u4 = create(new User( $this->db ))->get( $u->id );
		echo 'USER GET WITH ID:';
		echo '<br>';
		print_r( $u4->getModel() );
		echo '<br>';
		echo '<br>';


		$u5 = create(new User( $this->db ))->updateModel( ['id'=>$u4->id,  'email'=> 'abc2@b.pl','user_name'=>'abcd', 'password'=>'12345', ] );
		echo 'USER UPDATE:';
		echo '<br>before update:<br>';
		print_r( $u5->getModel() );
		echo '<br>';
		$vr = $u5->update();
		print_r($vr);
		echo '<br>after update:<br>';
		$u6 = create(new User($this->db))->get( $u5->id );
		print_r( $u6->getModel() );
		echo '<br>';
		echo '<br>';

		echo 'USERS LIST:';
		echo '<br>';
		$iter = User::getIterator( $this->db );
		$ulist = $iter->list();
		$iter->printItemsHtml( $ulist );
		echo '<br>';
		echo '<br>';

		echo 'USER REMOVE:';
		echo '<br>';
		$u->remove();
		print_r( $u->getModel() );
		echo '<br>';
		echo '<br>';

	}

}