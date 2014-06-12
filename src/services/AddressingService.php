<?php
namespace services;

use lib\model\Database;

class AddressingService{
	
	
	public function index(){
			
		require_once '../src/views/layout.php';

	}
	
	public function save($id=null)
	{
		if($_SERVER['REQUEST_METHOD']!="POST"){
			$data=array('response'=>'NOK','message'=>'Bad Method HTTP, attempt by POST method');
			echo $this->returnJson($data,"HTTP/1.0 404 Not Found");
			break;
		}
		
		$instance = Database::getInstance();
		$dbconn = $instance->getConnection();
		
		if($id!=null){
			
			$query = "SELECT * FROM ubications WHERE id = $1";			
			$prepare = pg_prepare($dbconn,'select', $query);			
			$result = pg_execute($dbconn, "select", array($id));				
			$c = pg_num_rows($result);
			
			if($c>0)
			{
				pg_query($dbconn,"BEGIN");					
				$query = "UPDATE ubications SET address=$1, lat=$2, lng=$3 WHERE id=$4";			
				$prepare = pg_prepare($dbconn,'update', $query);				
				$result = pg_execute($dbconn, "update", array($_POST['address'],$_POST['lat'],$_POST['lng'],$id));
				
				if($result===FALSE)
				{
					pg_query($dbconn,"ROLLBACK");
					$data=array('response'=>'NOK');
					echo $this->returnJson($data,"HTTP/1.0 404 Not Found");
						
				}else{
					pg_query($dbconn,"COMMIT");
					pg_free_result($result);
					$data=array('response'=>'OK');
					echo $this->returnJson($data);		
				}
					
			}else{
				pg_query($dbconn,"ROLLBACK");
				$data=array('response'=>'NOK');
				echo $this->returnJson($data,"HTTP/1.0 404 Not Found");		
			}
				
		}else{

			pg_query($dbconn,"BEGIN");
					
			$query = "INSERT INTO ubications (address,lat,lng) VALUES ($1, $2, $3)";		
			$prepare = pg_prepare($dbconn,'insert', $query);			
			$result = pg_execute($dbconn, "insert", array($_POST['address'],$_POST['lat'],$_POST['lng']));

			if($result===FALSE)
			{ 
				pg_query($dbconn,"ROLLBACK");
				$data=array('response'=>'NOK');
				echo $this->returnJson($data,"HTTP/1.0 404 Not Found");
					
			}else{
				pg_query($dbconn,"COMMIT");
				pg_free_result($result);
				$data=array('response'=>'OK');
				echo $this->returnJson($data);
				
			}
		}		
	}
	
	public function show($id)
	{
		$instance = Database::getInstance();
		$dbconn = $instance->getConnection();
		
		$query = "SELECT * FROM ubications WHERE id = $1";
		$prepare = pg_prepare($dbconn,'select', $query);
		$result = pg_execute($dbconn, "select", array($id));
		$c = pg_num_rows($result);
			
		if($c>0)
		{
			$ubication = pg_fetch_object($result);
			
			$data = array("id"=>$ubication->id,"address"=>$ubication->address,"latitude"=>$ubication->lat,"longitude"=>$ubication->lng);
			
			echo $this->returnJson($data);
		
		}else{
			$data=array('response'=>'NOK');
			echo $this->returnJson($data,"HTTP/1.0 404 Not Found");
		}
	}
	
	
	private function returnJson($data,$codehttp="HTTP/1.0 200 OK"){
		
		header('Content-type: application/json');
		header($codehttp);
		
		return json_encode($data);
	}
}