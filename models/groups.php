<?php 

namespace Okdev\Models;

use Okdev\Utils;

class Groups extends BaseModel {

	public function __construct($db) {
		$this->setDb( $db );
	}

	//akcja add bez parametrow : model + __init_defaults zostanie dodany do bazy (z pominięciem id)
	protected $model = array(
		'id' => 0,
		'title' => '',
		'id_user' => 0,
		'li_order' => 0,
		'descr' => ''
	);	
	
	protected function __init_defaults(&$m) {
	}


	public function listData($iduser, $idgroup) {
		$group_where = ' r.id_parent IS NULL ';
		if( $idgroup > 0 ) $group_where = ' r.id_parent = :idparent ';

		$g = $this->db->query( "
			SELECT g.title, g.descr, g.id
			FROM t_groups_rel r 
			LEFT JOIN t_groups g ON g.id = r.id_group
			WHERE g.id_user = :idu AND  " . $group_where 
			. " ORDER BY g.li_order ASC ",
			array(':idparent' => $idgroup, ':idu' => $iduser )
		);

		return $g['rows'];
	}

	public function isUserGroup( $iduser, $idgroup ) {
		if( $idgroup == 0 ) return true; //grupa 0 zawsze należy - wtedy id_group == null dla grup , oraz id_group == null i id_user == id_user dla items

		$g = $this->db->query_row( "SELECT title FROM t_groups WHERE id = :idg AND id_user = :idu ", 
			array( ':idg' => $idgroup, ':idu' => $iduser ) );

		return $g !== null;
	}

}