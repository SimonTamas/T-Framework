<?php

class Cacher extends Framework
{
	
	private static function initFolder($type)
	{
		if ( file_exists(constant_documentRoot . $type ) == false )
		{
			mkdir(constant_documentRoot . $type , 0777, true);
		}
	}
	
	public static function FilePathFromHref($href)
	{
		return str_replace(constant_websiteRoot,constant_documentRoot,$href);
	}
	
	public static function FileNameFromHref($href,$type)
	{
		return pathinfo($href)["filename"] . "." . $type;
	}
	
	public static function CacheExists($href,$type)
	{
		$fileName = self::FileNameFromHref($href,$type);
		$filePath = constant_documentRoot . $type . "/" . $fileName ;
		return file_exists($filePath);
	}
	
	public static function CacheIsFresh($href,$type)
	{
		$fileName = self::FileNameFromHref($href,$type);
		$cachePath = constant_documentRoot . $type . "/" . $fileName;
		$srcPath = self::FilePathFromHref($href);
		return file_exists($srcPath) && file_exists($cachePath) && filemtime($srcPath) - filemtime($cachePath) < 5;
	}
	
	public static function CreateCache($href,$string,$type)
	{
		self::initFolder($type);
		$fileName = self::FileNameFromHref($href,$type);	
		$cacheFile = fopen(constant_documentRoot . $type . "/" .$fileName ,"w");
		fwrite($cacheFile,$string);
		fclose($cacheFile);
	}
	
	public static function GetCacheHref($href,$type)
	{
		$fileName = self::FileNameFromHref($href,$type);
		return constant_websiteRoot . $type . "/" . $fileName;
	}
	
	public static function Cache($href,$type,$obfuscate=true,$compile=true,$advancedCompilation=false)
	{
		if ( !Cacher::CacheExists($href,$type) || !Cacher::CacheIsFresh($href,$type) )
		{
			$optimizedString = Framework::MinifyFromHref($href,$type,$obfuscate,$compile,$advancedCompilation);
			return Cacher::CreateCache($href,$optimizedString,$type);
		}
		else
		{
			return Cacher::GetCacheHref($href,$type);
		}
	}
}



?>