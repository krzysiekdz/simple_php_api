<?php 

namespace Okdev\Utils;

// use Okdev;

function array_copy($arr, $index=0) {
	$res = array();

	$size = count($arr);
	if($index >= $size || $index < 0) return $res;

	for($i = $index; $i < $size; $i++ ) {
		$res[] = $arr[$i];
	}

	return $res;
}

function remove_last($s) {
	return substr($s, 0, strlen($s)-1) ;
}

function checkRoute($route, $i, $url) {
	return isset($route[$i]) && $route[$i] == $url;
}

// function utf8_transform($e) {
// 	if(is_array($e)) {
// 		foreach($e as $k=>$v) {
// 			$e[$k] = utf8_transform($v);
// 		}
// 	} 
// 	else if (is_string($e)) {
// 		return utf8_encode($e);
// 	}
// 	return $e;
// }

function ret_json($res) {
	header('Content-Type: application/json; charset=utf-8');
	// echo json_encode( utf8_transform($res)  );
	echo json_encode( $res , JSON_UNESCAPED_UNICODE  );
}

function json_succ($d, $code = 1) {
	$d['code'] = $code;
	ret_json( $d );
}

function ret_not_found($msg = '', $code = -404) {
	if($msg == '') $msg = 'Nie znaleziono zasobu';
	return  array( 'code'=> $code, 'msg'=>$msg );
}

function ret_err($msg = '', $code = -1) {
	if($msg == '') $msg = 'Wystąpił błąd';
	return  array( 'code'=> $code, 'msg'=>$msg ) ;
}

function route_not_found($r = '[Nie podano]') {
	$msg = 'Nie znaleziono routingu dla: ' . $r ;
	return array( 'code'=>-404, 'msg'=>$msg ) ;
}

function getParam($name, $def='') {
	if(isset($_GET[$name])) return $_GET[$name];
	else if(isset($_POST[$name])) return $_POST[$name];
	else return $def;
}

function getParamInt($name, $def=0) {
	return (int) getParam( $name, $def );
}

function assert_int_pos( $v ) {
	return ( $v < 0 ) ? 0 : $v;
}

function assert_int_max( $v,  $min, $max ) {
	if ( $v < $min ) return $min;
	else if ( $v > $max ) return $max;
	return $v;
}

function isParam($name) {
	return isset($_GET[$name]) || isset($_POST[$name]);
}

function validateEmail($email) {
	return preg_match('/^[\w_\.]+@[\w]+\.[a-zA-Z]+$/', $email);
}

function validatePass($s) {
	return preg_match('/^.{4,}$/', $s);
}

function encode_pass($s) {
	return sha1( $s . Constants::$seed );
}

function create_token($iduser) {
	//zabezpieczenie przed wygenerowaniem dwa razy tego samego tokenu - iduser zabezpiecza - bez tego, jesli kilku uzytkownikow logowaloby sie w tej samej sekundzie to otrzymaliby te same tokeny! ale id user zabezpiecza - bo ten sam uzytkownik moze byc zalogowany tylko raz, wiec na pewno bedzie mial rozny token
	return sha1(  time() . Constants::$seed . date('Y-m-d h:i:s') . $iduser);
}

function date_now() {
	return date('Y-m-d H:i:s', time() - ( 4*60 + 30 ) );
	// return date('Y-m-d H:i:s', strtotime('- 4 minutes') );
}

// class Constants {
	
// }


