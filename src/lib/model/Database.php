<?php 
namespace lib\model;

class Database{
	
	private $engine;
	private $host;
	private $database;
	private $user;
	private $pass;
	private $port;
	private $dbconn;
	private static $instance;
	
	public function __construct(){
		
		$this->engine = 'pgsql';
		$this->host = 'localhost';
		$this->database = 'addresses';
		$this->user = 'softadmin';
		$this->pass = 'softadmin123';
		$this->port = '5432';
		$dns ="host=".$this->host." port=".$this->port.' dbname='.$this->database." user=".$this->user." password=".$this->pass;
		try {
			$this->dbconn = pg_connect($dns);
		} catch (Exception $e) {
    		echo 'Connection failed: ' . $e->getMessage();
		}
		
	}
	
	public static function getInstance() {
		
		if (self::$instance == null) {
			
			self::$instance = new Database();
			
		}
		
		return self::$instance;	
	}
	
	public function getConnection(){
		
		return $this->dbconn;
	}
	
}