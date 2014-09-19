<?php

class SqlServer extends Framework
{
	private $isConneted;
	private $mysqli;
	private $hostname;
	private $user;
	private $pass;
	
	private static $serverID = 0;

	public function Connect($user=constant_serverSqlUser,$pass=constant_serverSqlPassword,$db=constant_serverSqlDatabase)
	{
        $this->mysqli = new mysqli($this->hostname,$user,$pass,$db);
		//$this->debugger->Log("MYSQL_CONNECT");
	    $this->mysqli->query("SET NAMES UTF8");
	}
	
	public function FieldCount($result)
	{
		if ( $result )
		{
			return $result->field_count;
		}
		return 0;
	}
	
	public function FieldName($result,$offset)
	{
		if ( $result)
		{
			return $result->fetch_fields()[$offset]->name;
		}
		return "unknownFieldName";
	}
	
	public function NumRows($result)
	{
		if ( $result )
		{
			return $result->num_rows;
		}
		return 0;
	}
	
	public function Result($result,$rowOffset=0,$fieldOffset=0)
	{
		if ( $result )
		{
            $i = 0;
            while (  $i < $rowOffset  )
            {
                $result->fetch_row();
                $i++;
            }
            return $result->fetch_row()[$fieldOffset];
		}
		return "?";
	}
	
	public function Disconnect()
	{
		if ( $this->mysqli )
		{
            $this->mysqli->close();
		}
		$this->connection = null;
		$this->isConnected = null;
		$this->hostname = null;
		$this->pass = null;
		$this->user = null;
	}
	
	public function GetTable($tableName)
	{
		return $this->Query("SELECT * FROM " . $tableName);
	}
	
	public function GetTables($database=constant_serverSqlDatabase)
	{
		return $this->Query("SHOW TABLES FROM " . $database);
	}
	
	public function SelectDatabase($db=constant_serverSqlDatabase)
	{
        $this->mysqli->select_db($db);
	}
	
	public function Query($query)
	{
		$result = $this->mysqli->query($query);
		//$this->debugger->Log("MYSQLI_QUERY " . $query . " Error? => " . mysql_error() . "Rows : " . mysql_num_rows($result) );
		return $result;
	}
	
	public function __construct($autoConnect=false,$hostname=constant_serverSqlHostname)
	{
		$this->isConnected = false;
		$this->hostname = $hostname;
		
		self::$serverID++;
				
		// Create an instance of a debugger for the sqlServer
		//$this->debugger = new Debugger(parent::$debugFramework,"sqlServer" . self::$serverID);
		
		if ( $autoConnect )
		{
			$this->Connect();
			$this->SelectDatabase();
		}
	}
}

?>