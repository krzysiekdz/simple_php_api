<?php 

namespace Okdev\Utils;

$r = $_SERVER['DOCUMENT_ROOT'];

include_once $r.'/controllers/notes.php';
include_once $r.'/controllers/todos.php';
include_once $r.'/controllers/account.php';
include_once $r.'/controllers/groups.php';

use Okdev;
use Okdev\Controllers;
use Okdev\Utils\checkRoute;
use Okdev\Utils\ret_json;


class Routing {


	public static function route($route) {

		$route = explode('/', $route);
		array_shift($route);

		if( checkRoute($route, 0 , 'api') ) {
			if( checkRoute($route, 1, Controllers\Notes::$url) ) new Controllers\Notes($route, 2);
			else if( checkRoute($route, 1, Controllers\Todos::$url) ) new Controllers\Todos($route, 2);
			else if( checkRoute($route, 1, Controllers\Account::$url) ) new Controllers\Account($route, 2);
			else if( checkRoute($route, 1, Controllers\Groups::$url) ) new Controllers\Groups($route, 2);
			else ret_json( array('code'=>-404 , 'msg'=>'Nie znaleziono routingu') );
		}
		else {
			Okdev\Framework::show_home();
		}


	}




}


