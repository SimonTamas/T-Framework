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
	
	public static function FileNameFromHref($href)
	{
		return pathinfo($href)["filename"];
	}
	
	public static function CacheExists($href,$type,$uniqueID="")
	{
		$fileName = self::FileNameFromHref($href);
		$filePath = constant_documentRoot . $type . "/" . $uniqueID .  $fileName . "."  . $type ;
		return file_exists($filePath);
	}
	
	public static function CacheIsFresh($href,$type,$uniqueID="")
	{
		$fileName = self::FileNameFromHref($href);
		$cachePath = constant_documentRoot . $type . "/"  . $uniqueID . $fileName . "."  . $type ;
		$srcPath = self::FilePathFromHref($href);
		return file_exists($srcPath) && file_exists($cachePath) && filemtime($srcPath) - filemtime($cachePath) < 5;
	}
	
	public static function CreateCache($href,$string,$type,$uniqueID="")
	{
		self::initFolder($type);
		$fileName = self::FileNameFromHref($href);	
		$filePath = constant_documentRoot . $type . "/" . $uniqueID .  $fileName . "."  . $type;
		$cacheFile = fopen($filePath ,"w");
		fwrite($cacheFile,$string);
		fclose($cacheFile);
		return $filePath;
	}
	
	public static function GetCacheHref($href,$type,$uniqueID="")
	{
		$fileName = self::FileNameFromHref($href);
		return constant_websiteRoot . $type . "/" . $uniqueID . $fileName  . "."  . $type ; ;
	}
	
	public static function GetOrRequire($href,$var,$webPage,$uniqueID="")
	{
		if ( !Cacher::CacheExists($href,"html",$uniqueID) || !Cacher::CacheIsFresh($href,"html",$uniqueID) )
		{
			require($href);
			
			$folderPath = explode("/",$uniqueID);
			$folderCreate = "";
			foreach ( $folderPath as $folder )
			{
				$folderCreate .= $folder;
				self::initFolder("html/" . $folderCreate);
				$folderCreate .= "/";
			}
			
			return new Element("",array(),file_get_contents(Cacher::CreateCache($href,$$var->GetHTML(),"html",$uniqueID)));
		}
		else
		{
			return new Element("",array(),file_get_contents(Cacher::GetCacheHref($href,"html",$uniqueID)));
		}
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