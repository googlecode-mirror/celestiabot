<?php
class mysql {
	private $conn;
	private $host;
	private $user;
	private $pass;
	private $base;
	
	function __construct($host, $user, $pass, $base) {
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->base = $base;
	}
	
	public function connect() {
		$this->conn = mysql_connect($this->host, $this->user, $this->pass);
		mysql_select_db($this->base, $this->conn);
		mysql_query("SET CHARACTER SET utf8", $this->conn);
	}
	
	public function check() {
		if (!mysql_ping($this->conn)) {
			mysql_close($this->conn);
			$this->connect();
		}
	}
	
	public function query($q) {
		$this->check();
		return mysql_query($q);
	}
}
?>