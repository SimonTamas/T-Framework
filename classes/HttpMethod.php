<?php

class HttpMethod
{
	private $methodData;
	
	public function HasValue($key)
	{
		return array_key_exists($key,$this->methodData) == 1;
	}
	
	public function GetValue($key,$fallback=null)
	{
		if ( $this->HasValue($key)  )
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