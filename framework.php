<?php 

//-------------------- custom little framework

namespace Okdev;

//-------------------- include section

include 'utils/functions.php';
include 'data/db.php';
include 'models/core.php';
include 'controllers/core.php';
include 'utils/routing.php';

include 'config/data.php';

use OkDev\Utils;
use OkDev\Data;
use OkDev\Config;


//---------------------- run app section

session_start();
date_default_timezone_set('Europe/Warsaw');

Framework::main();

exit;

//w przyszlosci zrobic wlasny mini framework - przechowywany w odzielnym katalogu, ktory mozna kopiowac do projektow
    
//------------------------ framework class


class Framework {

	public static $db = null;

	public static function main() {

		try {

			self::$db = new Data\DB(Config\Data::$local_db);

			Utils\Routing::route( $_SERVER['REQUEST_URI'] );

			self::$db->close();
		} 
		catch (Exception $e) {
			print_r($e);
		}


	}


	public static function show_home() {
		include('./views/home.php');
	}
}






