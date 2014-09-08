<?php

class Span extends Element
{
	public function __construct($spanHTML,$spanProperties)
	{
		parent::__construct("span",array( "src" => $imageSrc , "alt" => $imageAlt , "title" => $imageTitle ));
	}
}

?>