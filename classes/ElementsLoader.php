<?php

class ElementsLoader
{
	public static function LoadElementsFrom($webPage,$pageName,$pagePart)
	{
		$elementsPath = constant_pathToParts . $pageName . "/" . $pageName . "_" . $pagePart . ".php";
		if ( file_exists($elementsPath) )
		{
			require_once($elementsPath);
		}
	}
	
	public static function PageExists($webPage,$pageName)
	{
		return file_exists(constant_pathToParts . $pageName);
	}
	
	public function LoadElements($pageName,$pagePart)
	{
		
	}
	
	public function __construct()
	{
		
	}
}


?>