<?php


class Obfuscator
{
	public static $obfuscatedCache = array();
	public static $nameExceptions = array("fb-root","fb-like-box");
	
	public static function AddExceptions($exceptions)
	{
		for ( $i = 0 ; $i < count($exceptions) ; $i++ )
		{
			self::AddException($exceptions[$i]);
		}
	}
	
	public static function AddException($name)
	{
		array_push(self::$nameExceptions,$name);
	}
	
	public static function IsException($name)
	{
		for ( $i = 0 ; $i < count(self::$nameExceptions) ; $i++ )
		{ 
			$pos = strpos($name,self::$nameExceptions[$i]);
			if ( $pos !== false && $pos == 0 )
			{
				return true;
			}
		}
		return false;
	}
	
	public static function GetCodedString($string)
	{
		$sql = new SqlServer(true);
		$nameKeys = $sql->Query("SELECT varName,varType,varKey FROM obfuscatednames ORDER BY CHAR_LENGTH(varName) DESC");
		$keysArray = SqlServer::ResultArray($nameKeys);
		for ( $i = 0 ; $i < count($keysArray) ; $i++ )
		{
			$name = $keysArray[$i][0];
			if ( !self::IsException($name) )
			{
				$type = $keysArray[$i][1];
				$key = $keysArray[$i][2];
				if ($type == "id" )
				{
					$string = str_replace("#".$name,"&tempId".$key,$string);
				}
				else if ( $type = "class")
				{
					$string = str_replace(".".$name,"&tempClass".$key,$string);
				}
			}
		}
		$sql->Disconnect();
		$sql = null;
		// Just to make sure we dont obfuscate TWICE
		$string = str_replace("&tempId","#",$string);
		$string = str_replace("&tempClass",".",$string);
		return $string;
	}
	
	public static function GetCode($num)
	{
		$result = "";
		$count = 0;
		while ($num>=1)
		{
		    if ($num < 26) $offset=9; else $offset=10;
		    $remainder = $num%26;
		    $digit =  base_convert($remainder+$offset,10,36);
		    $result .= $digit;
		    $num = floor($num/26);
		    $count++;
		}
		$result = strrev($result);
		return $result;
	}
	
	
	public static function GetKey($name,$type)
	{
		if ( self::IsException($name) ) 
		{
			return $name;
		}	
		// Dont connec to server if we already obfuscated the class 
		// id should not come up more then once
		if ( array_key_exists($name."/".$type, self::$obfuscatedCache))
		{
			if ( $type == "id" )
			{
				// Duplicate id, not W3C valid!
				//Debugger::JustLog("Found duplicate ID : " . $name);
			}
			return self::$obfuscatedCache[$name."/".$type];
		}
		$sql = new SqlServer(true);
		$key = $sql->Query("SELECT varKey FROM obfuscatedNames WHERE varName = '" . $name . "' AND varType = '" .$type."'");
		if ( $sql->NumRows($key) > 0 )
		{
			$gotKey = $sql->Result($key);
			self::$obfuscatedCache[$name."/".$type] = $gotKey;
			return $gotKey;
		}
		else
		{
			$lastEntry = $sql->Query("SELECT entry FROM obfuscatedNames ORDER BY entry DESC LIMIT 1");
			$lastEntryNr = 0;
			if ( $sql->NumRows($lastEntry) > 0 )
			{
				$entryResult = $sql->Result($lastEntry);
				if ( $entryResult != null && strlen($entryResult) > 0 )
				{
					$lastEntryNr = $entryResult;
				}
			}
			$newKey = self::GetCode($lastEntryNr+1);
			self::$obfuscatedCache[$name."/".$type] = $newKey;
			$sql->Query("INSERT INTO obfuscatedNames (varName,varType,varKey) VALUES ('".$name."','".$type."','".$newKey."')");
			return $newKey;
		}
		$sql->Disconnect();
		$sql = null;
	}
}