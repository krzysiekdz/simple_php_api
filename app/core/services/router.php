<?php 

namespace Okdev;


class Router {


	public  function detect($url, $i) {
		$c = null;
		if( $this->checkRoute( $url, $i, 'account' ) ) $c = new AccountController('account');

		if( $c ) {
			$this->prepareController( $c, $url, $i );
		}
		

		return $c;
	}


	protected function prepareController( &$c, $url, $i ) {
		$c->setDb();
		$c->setRoute( $url, $i+1 );	
	}

	protected function checkRoute($url, $i, $path) : bool {
		$r = explode('/', $url);
		if( count($r) == 0 ) return false; 
		if( $r[0] == '' ) array_shift($r);

		return isset($r[$i]) && ($r[$i] == $path || strpos($r[$i], $path.'?') !== false );
	}

	

}


