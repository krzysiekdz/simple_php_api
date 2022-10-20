<?php

namespace Okdev;

//buses
class App {
	public function connectDb() { return null; } //new Data\DB(Config\Data::$local_db);

	public function run() {
		// Utils\Routing::route( $_SERVER['REQUEST_URI'] );
		echo 'buses run';
	}
}