<?php 

namespace Okdev\Controllers;

include $_SERVER['DOCUMENT_ROOT'].'/models/note.php';

use Okdev\Utils;
use Okdev\Models;

class Notes extends BaseController {
	
	public static $url = 'notes';

	protected $table_name = 't_notes';

	protected function createModel() { return new Models\Note(); }

	public  function __construct($r, $i) {
		parent::__construct($r, $i, self::$url);
	}

	
}