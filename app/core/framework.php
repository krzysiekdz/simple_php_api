<?php 

//-------------------- custom little framework

namespace Okdev;

//-------------------- base include section

include 'utils/functions.php';
include 'services/db.php';
include 'services/router.php';
// include 'models/core.php';
// include 'controllers/core.php';


//---------------------- run app section

session_start();
date_default_timezone_set('Europe/Warsaw');

Framework::main();

exit;

    
//------------------------ framework class


class Framework {

	public static $db = null;
	public static $app = null; //obiekt klasy App, ktory utworzy sie przy inicjalizacji aplikacji

	public static function base() { return $_SERVER['DOCUMENT_ROOT']; }

	public static function main() {

		try {
			$r = explode('/', $_SERVER['REQUEST_URI']);
			array_shift( $r );
			$base = $r[0];
			//document root to np: /home/okdevhmc/domains/okdev.hmcloud.pl/private_html
			$bootstrap = $_SERVER['DOCUMENT_ROOT'] .'/app/' . $base . '/bootstrap.php';
			if( file_exists( $bootstrap ) ) {
				include( $bootstrap ); //ładowanie konkretnej aplikacji oraz jej klasy wejściowej: App
			}
			else {
				throw new \Exception("Project \"$base\" not configured: bootstrap.php is missing.");
			}

			self::$app = new App();

			self::$db = self::$app->connectDb();

			self::$app->run();

			self::$app->onClose();

			if( self::$db ) { self::$db->close(); }
		} 
		catch (\Exception $e) {
			print_r($e->getMessage());
		}


	}


	public static function show_home() {
		include('./views/home.php');
	}
}






