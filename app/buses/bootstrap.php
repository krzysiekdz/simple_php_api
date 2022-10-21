<?php

namespace Okdev;

include Framework::base().'/config/buses/data.php';
include 'routing.php';

//--------- tutaj specyficzne includy dla projektu


//buses
class App {
	public function connectDb() : Services\Db { return new Services\Db( Config\Data::$local_db ); } 

	public function run() {
		// Utils\Routing::route( $_SERVER['REQUEST_URI'] );
		echo 'buses run';
	}

	public function onClose() {} 
}