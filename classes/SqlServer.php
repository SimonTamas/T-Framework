<?php

class SqlServer extends Framework
{
	private $isConneted;
	private $connection; 	
	private $hostname;
	private $user;
	private $pass;
	
	private static $serverID = 0;

	public function Connect($user=constant_serverSqlUser,$pass=constant_serverSqlPassword,$db=constant_serverSqlDatabase)
	{
		$this->connection = mysql_connect($this->hostname,$user,$pass);
		//$this->debugger->Log("MYSQL_CONNECT");
	    mysql_query("SET NAMES UTF8"); 
	}
	
	public function FieldCount($result)
	{
		if ( is_resource($result) )
		{
			return mysql_num_fields($result);
		}
		return 0;
	}
	
	public function FieldName($result,$offset)
	{
		if ( is_resource($result) )
		{
			return mysql_field_name($result,$offset);
		}
		return "unknownFieldName";
	}
	
	public function NumRows($result)
	{
		if ( is_resource($result) )
		{
			return mysql_num_rows($result);
		}
		return 0;
	}
	
	public function Result($result,$rowOffset=0,$fieldOffset=0)
	{
		if ( is_resource($result) )
		{
			return mysql_result($result,$rowOffset,$fieldOffset);
		}
		return "?";
	}
	
	public function Disconnect()
	{
		if ( $this->connection )
		{
			mysql_close($this->connection);
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
		 mysql_select_db($db);
	}
	
	public function Query($query)
	{
		$result = mysql_query($query);
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