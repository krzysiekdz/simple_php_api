<?php

namespace Okdev;

//--------- specyficzne includy dla projektu
include Framework::base().'/config/buses/data.php';
include 'routing.php';



//buses
class App {
	public function connectDb() : Services\Db { return new Services\Db( Config\Data::$local_db ); } 

	public function run() {
		$router = new BusesRouter();
		$controller = $router->detect( $_SERVER['REQUEST_URI'], 1 );
		$controller->run();
	}

	public function onClose() {} 
}