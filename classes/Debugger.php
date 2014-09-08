<?php

class Debugger extends Framework
{
	private $doDebug;
	private $fileID;
	private static $folderPath;
	private static $errorPath;
	
	private $logFilePath;
	
	public static function JustLog($string)
	{
		if ( Framework::$debugFramework )
		{
			$logFile = fopen(constant_pathToDebugLog . "/log.txt","a");
			fwrite($logFile, $string . "\r\n");
			fclose($logFile);
		}
	}
	
	public function Log($string,$error=false)
	{
		if ( $this->doDebug )
		{
			$this->initFile($this->fileID,$error);
			$logFile = fopen($this->logFilePath,"a");
			fwrite($logFile, $string . "\r\n");
			fclose($logFile);
		}
	}
	
	public static function SetDocument($setDocument)
	{
		self::$folderPath = constant_pathToDebugLog . $setDocument;
	}
	
	private function initFolder()
	{
		if ( file_exists(self::$folderPath) == false ) 
		{
		    mkdir(self::$folderPath, 0777, true);
		}
		if ( file_exists(self::$errorPath) == false )
		{
			mkdir(self::$errorPath, 0777, true);
		}
	}
	
	private function initFile($fileStamp,$isError)
	{
		$year = date('Y');
		$month = date('m');
		$day = date('d');
		$hour = date('H');
		$minutes = date('i');
		$dateStamp = $year . "-" . $month . "-" . $day ." " . $hour . "-" . $minutes;
		if ( !$isError )
		{
			$this->logFilePath = self::$folderPath . "/" .  $dateStamp . " " . $fileStamp . ".txt";
		}
		else
		{
			$this->logFilePath = self::$errorPath . "/" .  $dateStamp . " " . $fileStamp . ".txt";
		}
	}
	
	function __construct($doDebug,$fileID) 
	{
		self::$errorPath = constant_pathToDebugLog . "errors";
		$this->fileID = $fileID;
		$this->doDebug = $doDebug;	
		if ( $doDebug )
		{
			$this->initFolder();
		}
	}
}

?>