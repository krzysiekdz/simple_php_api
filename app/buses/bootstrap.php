<?php

namespace Okdev;

//--------- specyficzne includy dla projektu
include Framework::base().'/config/buses/data.php';
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

		BusTest::runAll( $this->db );
		// $router = new BusesRouter();
		// $controller = $router->detect( $_SERVER['REQUEST_URI'], 1 );
		// $controller->run();
	}

	public function onClose() {} 


	
}