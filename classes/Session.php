<?php 


class Session
{
	public static function Get($key,$fallback=null)
	{
		self::TryToContinue();
		if ( self::Exists($key) )
		{
			return $_SESSION[$key];
		}
		return $fallback;
	}
	
	public static function Destroy($key)
	{
		self::TryToContinue();
		unset($_SESSION[$key]);
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
	
	public static function Id()
	{
		self::TryToContinue();
		return session_id();
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