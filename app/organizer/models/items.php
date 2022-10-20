<?php 

namespace Okdev\Models;

use Okdev\Utils;

class Items extends BaseModel {

	public function __construct($db) {
		$this->setDb( $db );
	}

	//akcja add bez parametrow : model + __init_defaults zostanie dodany do bazy (z pominięciem id)
	protected $model = array(
		'id' => 0,
		'title' => '',
		'type' => '',
		'descr' => '',
		'add_time' => '',
		'modif_time' => '',
		'id_user_modif' => 0,
		'li_order' => 0
	);	
	
	protected function __init_defaults(&$m) {
	}


	public function listData($iduser, $idgroup,  $start, $limit) {
		$where = ' r.id_group = :idg ';
		if( $idgroup <= 0 ) $where = ' r.id_user = :idu ';

		$items = $this->db->query( "
			SELECT i.*
			FROM t_items_rel r 
			LEFT JOIN t_items i ON i.id = r.id_item
			WHERE " . $where 
			. " ORDER BY i.li_order ASC 
				LIMIT :start, :limit
			",
			array( ':idg' => $idgroup, ':idu' => $iduser, ':start' => $start, ':limit' => $limit )
		);

		return $items['rows'] ;
	}

}