<?php

class Style extends Element
{
	public function __construct($styleHref,$device="all")
	{
		$styleHref = Cacher::Cache($styleHref,"css");
		parent::__construct("link",array( "href" => $styleHref , "rel" => "stylesheet" , "type" => "text/css", "media" => $device));
	}
}

?>