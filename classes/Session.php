<?php 


class Session
{
	public static function Get($key)
	{
		self::TryToContinue();
		return $_SESSION[$key];
	}
	
	public static function Set($key,$value)
	{
		self::TryToContinue();
		$_SESSION[$key] = $value;
	}
	
	public static function Exists($key)
	{
		self::TryToContinue();
		return isset($_SESSION[$key]);
	}
	
	private static function TryToContinue()
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
		self::TryToContinue();
	}
}