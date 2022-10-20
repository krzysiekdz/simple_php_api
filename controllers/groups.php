<?php 

namespace Okdev\Controllers;

include_once $_SERVER['DOCUMENT_ROOT'].'/models/groups.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/models/items.php';

use Okdev\Utils;
use Okdev\Models;

class Groups extends BaseController {
	
	public static $url = 'groups';

	//nadpisanie wartosci domyslnych z BaseController
	protected $def_limit = 10;
	protected $max_limit = 100;

	protected function createModel() { return new Models\Groups( $this->db ); }


	public  function __construct($r, $i) {
		parent::__construct($r, $i, self::$url);
	}


	public function action_list() {
		if( !$this->checkSession() ) return;

		$g = $this->createModel();
		$i = new Models\Items( $this->db );

		parent::parseListParams();
		$id_user = (int) $this->session->user['id'];
		
		$id_group = Utils\getParamInt( 'idgroup' ); 
		if( ! $g->isUserGroup( $id_user, $id_group ) ) {
			$this->resultErr( 'Próbujesz wyświetlić kategorię która nie istnieje' );
			return;
		}
		
		$res_g = array();
		if( $this->start == 0 ) {//jesli okaze sie, ze po grupach tak samo bedzie trzeba przechodzic przy pomocy start, limit, to mozna to dorobic: gstart, glimit
			$res_g = $g->listData( $id_user, $id_group );
		}

		$res_i = $i->listData( $id_user, $id_group, $this->start, $this->limit );

		$this->setResult( array('groups' => $res_g, 'items' => $res_i ) );
	}

}