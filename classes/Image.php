<?php

class Image extends Element
{
	public function __construct($imageSrc,$imageAlt=" ",$imageTitle="",$properties=array())
	{
		$properties["src"] = $imageSrc;
		$properties["alt"] = $imageAlt;
		$properties["title"] = $imageTitle;
		parent::__construct("img",$properties);
	}
}

?>