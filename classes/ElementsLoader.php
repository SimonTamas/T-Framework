<?php

class ElementsLoader extends Framework
{
	public static function LoadElementsFrom($webPage,$pageName,$pagePart)
	{
		$elementsPath = constant_pathToParts . $pageName . "/" . $pageName . "_" . $pagePart . ".php";
		if ( file_exists($elementsPath) )
		{
			require_once($elementsPath);
		}
	}
	
	public function LoadElements($pageName,$pagePart)
	{
		
	}
	
	public function __construct()
	{
		
	}
}


?>