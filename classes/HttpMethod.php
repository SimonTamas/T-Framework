<?php

class HttpMethod extends Framework
{
	private $methodData;
	
	public function HasValue($key)
	{
		return array_key_exists($key, $this->methodData);
	}
	
	public function GetValue($key,$fallback="")
	{
		if ( $this->HasValue($key) )
		{
			return $this->methodData[$key];
		}
		return $fallback;
	}
	
	public function processMethods()
	{
		$this->methodData = array();
		foreach($_POST as $key => $value) 
		{
			$this->methodData[$key] = $value;
		}
		foreach($_GET as $key => $value)
		{
			$this->methodData[$key] = $value;
		}
	}
	
	function __construct()
	{
		$this->processMethods();
	}
}