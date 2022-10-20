<?php

namespace Okdev;

//organizer
class App {
	public function connectDb() { return null; }

	public function run() {
		// Utils\Routing::route( $_SERVER['REQUEST_URI'] );
		echo 'org run';
	}
}