<?php

class Framework 
{
	public $debugger;
	public static $debugFramework;
	
	
	public static function Minify($buffer)
	{
		// Remove comments
		$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
		
		// Replace root with website_root
		$buffer = str_replace('$root',constant_websiteRoot,$buffer);
		
		// Replace every occurance of name with key
		$buffer = Obfuscator::GetCodedString($buffer);
		
		// Remove space after colons
		$buffer = str_replace(': ', ':', $buffer);
		
		// Remove whitespace
		$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
		
		return $buffer;
	}
	
	public static function MinifyJS($href,$obfuscate=true)
	{
		$c = new PhpClosure();
		$c->_debug = false;
		$compiled = $c->add($href)->returnJS();
		if ( $obfuscate )
		{
			return Obfuscator::GetCodedString($compiled);
		}
		return $compiled;
	}
	
	public static function MinifyFromHref($href,$type,$obfuscate=true)
	{
		if ( $type == "css" )
		{
			return self::Minify(file_get_contents($href));
		}
		else if ( $type == "js" )
		{
			return self::MinifyJS($href,$obfuscate);
		}
		else
		{
			return file_get_contents($href);
		}
	}
	
	public static function SetDebug($to)
	{
		self::$debugFramework = $to;
	}
	
	public static function CreateTables()
	{
		$sql = new SqlServer(true);
		
		// LanguageQuery
		$langQuery = "CREATE TABLE IF NOT EXISTS `language` (`entry` int(11) NOT NULL AUTO_INCREMENT,`langKey` varchar(50) DEFAULT NULL,`hu` mediumtext CHARACTER SET utf8,`eng` mediumtext CHARACTER SET utf8,PRIMARY KEY (`entry`)) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1";
		
		// Obfuscated names Query
		$obfsQuery = "CREATE TABLE IF NOT EXISTS `obfuscatednames` (`entry` int(11) NOT NULL AUTO_INCREMENT, `varName` varchar(50) DEFAULT NULL,`varType` varchar(5) DEFAULT NULL,`varKey` varchar(10) DEFAULT NULL,PRIMARY KEY (`entry`)) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1";
		
		$sql->Query($langQuery);
		$sql->Query($obfsQuery);
		
		$sql->Disconnect();
		$sql = null;
	}
}

?>