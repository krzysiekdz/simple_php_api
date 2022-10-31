<?php 

namespace Okdev;


class HomeController extends BaseController {

	protected $def_list = false;
	protected $def_get = false;
	
	public function action_news() {
		$this->callController( new HomeNewsController('news') );
	}
}

class HomeNewsController extends BaseController {

	protected function createIterator() { return HomeNews::getIterator( $this->db ); }

	protected function createModel() { return new HomeNews( $this->db ); }
}

class HomeGalleryController extends BaseController {
	
}