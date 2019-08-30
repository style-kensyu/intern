<?php

class db{
    // __construct
    public function __construct()
    {
        // $this->initDB();

		if($this->initDB()){
			$this->createDB();
		}

    }

		// db 作成
	public function createDB(){
		$dbname 	= "mysql:host=localhost;charset=utf8mb4;";
	    $usrname 	= "root";
		$psword 	= "";

      	try{
  	    	$this->db = new PDO($dbname, $usrname, $psword);
			$sql = "CREATE DATABASE schedule";
			$this->db->exec($sql);
			$this->createTable();
		}catch(PDOException $e){
          echo $e->getMessage();
          exit;
     	}
	}

	public function createTable(){
		// 接続
		$this->initDB();

		$sql = <<<SQL
CREATE TABLE `userschedule` (
`sid` int(11) NOT NULL,
`text` varchar(194) CHARACTER SET utf8mb4 NOT NULL,
`type` int(11) NOT NULL,
`reg_date` datetime NOT NULL,
`status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `userschedule`
ADD PRIMARY KEY (`sid`);

ALTER TABLE `userschedule`
MODIFY `sid` int(11) NOT NULL AUTO_INCREMENT
SQL;

	$this->query($sql);

	}

    // db接続
    public function initDB(){
    	$dbname 	= "mysql:dbname=schedule;host=localhost;charset=utf8mb4;";
	    $usrname 	= "root";
		$psword 	= "";

      	try{
  	    	$this->db = new PDO($dbname, $usrname, $psword);
			return false;
      	}catch(PDOException $e){
          	// echo $e->getMessage();
			return true;
          	// exit;
      	}
    }
}

?>
