<?php

use JShrink\Minifier;
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
		
		// Remove space after colons
		$buffer = str_replace(': ', ':', $buffer);
		
		// Remove whitespace
		$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
		
		return $buffer;
	}
	
	public static function MinifyJS($href)
	{
		$c = new PhpClosure();
		$c->_debug = false;
		return $c->add($href)->returnJS();
	}
	
	public static function MinifyFromHref($href,$type)
	{
		if ( $type == "css" )
		{
			return self::Minify(file_get_contents($href));
		}
		else if ( $type == "js" )
		{
			//return \JShrink\Minifier::minify(file_get_contents($href));
			return self::MinifyJS($href);
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
}

?>