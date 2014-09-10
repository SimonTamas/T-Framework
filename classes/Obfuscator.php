<?php


class Obfuscator extends Framework
{
	public static $obfuscatedCache = array();
	public static $nameExceptions = array("fb-root","fb-like-box");
	
	public static function GetCodedString($string)
	{
		$sql = new SqlServer(true);
		$nameKeys = $sql->Query("SELECT varName,varType,varKey FROM obfuscatednames ORDER BY CHAR_LENGTH(varName) DESC");
		for ( $i = 0 ; $i < $sql->NumRows($nameKeys) ; $i++ )
		{
			$name = $sql->Result($nameKeys,$i);
			if ( !in_array($name, self::$nameExceptions) )
			{
				$type = $sql->Result($nameKeys,$i,1);
				$key = $sql->Result($nameKeys,$i,2);
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
		if ( in_array($name, self::$nameExceptions) ) 
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
				Debugger::JustLog("Found duplicate ID : " . $name);
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