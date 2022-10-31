<?php 

namespace Okdev;


class BusesRouter extends Router {


	public function detect($url, $i) {
		$c = null;
		if( $this->checkRoute( $url, $i, 'home' ) ) $c = new HomeController('home');

		if( $c ) {
			$this->prepareController( $c, $url, $i );
			return $c;
		}

		return parent::detect( $url, $i );
	}




}


