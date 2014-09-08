<?php

class Script extends Element
{
	private $scriptSrc;
	private $scriptType;
	private $scriptCharset;
	private $boolLoadAsync;
	
	
	public function __construct($scriptSrc,$scriptType="text/javascript",$scriptCharset="UTF-8")
	{
		$scriptSrc = Cacher::Cache($scriptSrc,"js");
		parent::__construct("script",array( "src" => $scriptSrc , "type" => $scriptType , "charset" => $scriptCharset));
	}
}

?>