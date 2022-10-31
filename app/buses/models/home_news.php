<?php 

namespace Okdev;


class HomeNews extends BaseModel {

	protected $table_name = 't_home_news';

	protected $model = array(
		'id' => 0,
		'title' => '',
		'content' => '',
		't_order' => 0,
		't_visible' => 1,
	);	

	
	protected function validate(array &$m, bool $create) : ValidateResult {
		return new ValidateResult(1, '');
	} 

	protected function afterCreateUpdate(array &$m, bool $create) {
	} 

	protected function afterGet( &$m ) {

	}

	protected function afterListItem( &$m ) {

	}

	public static function getIterator($db) : HomeNews {
		$it = new HomeNews( $db );
		$it->select('id, title, content')->where('t_visible = 1')->orderBy('t_order ASC')->itemClass( HomeNews::class );
		return $it;
	}

}