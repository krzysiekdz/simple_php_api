<?php 

namespace Okdev\Data;

use Okdev\Utils;


/*
 ALTER TABLE `okdevhmc_okdb`.`t_todos`  MODIFY `text` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_polish_ci;
*/


class DB {

	protected $conn;
	public $log = array();

	public  function __construct($c) {
		//nawiązanie połączenia z baza
		//persisent connection? - czy uzywac? - wtedy dodaje sie prefix 'p:'
		$this->conn = new \mysqli(
			$c['host'], 
			$c['user_name'], 
			$c['pass'], 
			$c['db_name']
		);


		if($this->conn->connect_error) {
			die('Connection failed: ' . $this->conn->connect_error);
		}

		// echo 'initial charset is :' . $this->conn->character_set_name(); // latin1

		$this->conn->set_charset('utf8'); //kodowanie utf8

	}

	public function getConn() {
		return $this->conn;
	}

	public function close() {
		$this->conn->close();
	}

	public function query($sql, $params = array()) {
		if( count( $params ) > 0 ) {
			foreach( $params as $k=>$v ) {
				if( substr($k, 0, 2) == 's:' ) { $v = "'".$v."'"; } //s: - oznacza, ze chcemy przekazac string, czyli dodajemy ''

				$sql = str_replace($k, $v, $sql);
			}
		}

		$this->log[] = $sql; //dodanie zapytania do 'logu'
		$res = $this->conn->query($sql);

		$buff = [];

		if( isset( $res->num_rows ) && $res->num_rows > 0) {
			while($row = $res->fetch_assoc()) {
				$buff[] = $row;
			}
		}

		$d = array('size'=> count($buff), 'rows'=>$buff );
		if( count($buff) == 1 ) {
			$d['row'] = $buff[0];
		}		
		return $d;
	}

	public function query_row($sql, $params = array()) {
		$res = $this->query( $sql, $params );
		if( $res['size'] > 0 ) return $res['rows'][0];
		return null;
	}

	public function list_data($table_name, $start = 0, $limit = 10) {
		if($this->conn == null) return array('code'=>-1);

		$sql = "SELECT * FROM $table_name LIMIT $start, $limit";

		$this->log[] = $sql; //dodanie zapytania do 'logu'

		$res = $this->conn->query($sql);

		$buff = [];

		if($res->num_rows > 0) {
			while($row = $res->fetch_assoc()) {
				$buff[] = $row;
			}
		}

		return array('size'=> count($buff), 'rows'=>$buff , 'code'=>1 );
	}

	public function get_by_id($table_name, $id) {
		if($this->conn == null) return array('code'=>-1);

		$sql = "SELECT * FROM $table_name WHERE id = $id";

		$this->log[] = $sql; //dodanie zapytania do 'logu'

		$res = $this->conn->query($sql);

		$buff = [];
		if($res->num_rows > 0) {
			if($row = $res->fetch_assoc()) {
				$buff[] = $row;
			}
		}

		if( count( $buff ) > 0 ) {
			$row = $buff[0];
			return array( 'row'=>$row, 'code'=>1 );
		}
		else {
			return array( 'code'=> -1 );
		}
		
	}

	public function insert_data($table_name, $data) {
		if($this->conn == null) return array('code'=>-1, 'msg'=>'Lost DB connection');

		$sql = "INSERT INTO $table_name ";
		$keys = '(';
		$vals = ' VALUES (';

		foreach($data as $key=>$val) {
			$keys .= $key . ',';
			$vals .= '\''. $val . '\',';
		}
		$keys = Utils\remove_last($keys) . ')';
		$vals = Utils\remove_last($vals) . ')';

		$sql .= $keys . $vals;

		$this->log[] = $sql; //dodanie zapytania do 'logu'

		$res = array();
		if( $this->conn->query($sql) === true) {
			$id = $this->conn->insert_id;
			$res['code'] = 1;
			$res['id'] = $id;
		} 
		else {
			$res['code'] = -1;
			$res['msg'] = $this->conn->error;
			$res['sql'] = $sql;
		}

		
		return $res;
	}

	public function update_data($table_name, $data) {
		$res_bad = array('code' => -1);

		if($this->conn == null) return $res_bad;

		if( !isset($data['id']) ) return $res_bad;

		$id = $data['id'];
		unset($data['id']);

		if( count($data) == 0 ) return $res_bad;

		$sql = "UPDATE $table_name SET ";
		$sql_where = " WHERE id = $id";

		foreach($data as $key=>$val) {
			$sql .= " $key = '$val',";
		}
		
		$sql = Utils\remove_last($sql) . $sql_where;

		$this->log[] = $sql; //dodanie zapytania do 'logu'

		$res = array();
		if( $this->conn->query($sql) === true) {
			$res['code'] = 1;
			$res['id'] = $id;
		} 
		else {
			$res['code'] = -1;
			$res['msg'] = $this->conn->error;
			$res['sql'] = $sql;
		}

		
		return $res;
	}

}