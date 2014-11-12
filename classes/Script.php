<?php

class Script extends Element
{
	private $scriptSrc;
	private $scriptType;
	private $scriptCharset;
	private $boolLoadAsync;
	
	
	public function __construct($scriptSrc,$obfuscateScript=true,$compileScript=true,$advancedCompilation=false,$scriptType="text/javascript",$scriptCharset="UTF-8")
	{
		$scriptSrc = Cacher::Cache($scriptSrc,"js",$obfuscateScript,$compileScript,$advancedCompilation);
		if ( strlen($scriptSrc) > 0 )
		{
			if ( Framework::$inlineHead  )
			{
				parent::__construct("script",array("type" => $scriptType , "charset" => $scriptCharset),file_get_contents($scriptSrc));
			}
			else 
			{
				parent::__construct("script",array( "src" => $scriptSrc , "type" => $scriptType , "charset" => $scriptCharset));
			}
		}
	}
}

?>