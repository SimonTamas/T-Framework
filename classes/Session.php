<?php 


class Session
{
	public static function Get($key)
	{
		return $_SESSION[$key];
	}
	
	public static function Set($key,$value)
	{
		$_SESSION[$key] = $value;
	}
	
	public static function Exists($key)
	{
		return isset($_SESSION[$key]);
	}
	
	private function TryToContinue()
	{
		$sessionID = Cookie::Get("PHPSESSID");
		if ( $sessionID != null && strlen($sessionID) > 0 )
		{
			session_id($sessionID);
		}
		if ( session_status() == PHP_SESSION_NONE )
		{
			session_start();
		}
	}
	
	public function __construct()
	{
		$this->TryToContinue();
	}
}