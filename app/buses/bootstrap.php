<?php

namespace Okdev;

//--------- specyficzne includy dla projektu
include Framework::base().'/config/buses/data.php';

include 'models/home_news.php';

include 'controllers/home.php';

include 'routing.php';

include 'test.php';



//buses
class App {
	protected $db = null;

	public function connectDb() : DB { 
		$this->db = new DB( Buses\Config::$local_db ); 
		return $this->db; 
	} 

	public function run() {
		User::$adminKey = Buses\Config::$adminKey;
		Constants::$seed = Buses\Config::$seed;

		// BusTest::runAll( $this->db );

		//kontynuowac teraz aplikacje i dorabiac na biezaco rzeczy
		$router = new BusesRouter();
		$controller = $router->detect( $_SERVER['REQUEST_URI'], 1 );
		if( $controller )  {
			$controller->run();
			$controller->resultJson(); //oznacza, że 'buses' jest api REST-owym, bo zwracamy json
		}
		else {
			ret_json( [ 'code' => '-404', 'msg' => 'Nie znaleziono!' ] );
		}
	}

	public function onClose() {} 


	
}