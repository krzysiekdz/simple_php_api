<?php 

namespace Okdev\Controllers;

include $_SERVER['DOCUMENT_ROOT'].'/models/todo.php';

use Okdev\Utils;
use Okdev\Models;

class Todos extends BaseController {
	
	public static $url = 'todos';

	protected $table_name = 't_todos'	;

	protected function createModel() { return new Models\Todo(); }

	public  function __construct($r, $i) {
		parent::__construct($r, $i, self::$url);
	}

	
}