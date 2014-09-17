<?php

class Style extends Element
{
	public function __construct($styleHref,$device="all")
	{
		$styleHref = Cacher::Cache($styleHref,"css");
		if ( Framework::$inlineHead )
		{
			parent::__construct("style",array( "type" => "text/css"),file_get_contents($styleHref));
		}
		else
		{
			parent::__construct("link",array( "href" => $styleHref , "rel" => "stylesheet" , "type" => "text/css", "media" => $device));
		}
	}
}

?>