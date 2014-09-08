<?php 


class Session
{
	public static function Get($key)
	{
		if ( isset($_SESSION[$key]) )
		{
			return $_SESSION[$key];
		}
		return "";
	}
	
	private function TryToContinue()
	{
		$sessionID = Cookie::Get("PHPSESSID");
		if ( $sessionID != null && strlen($sessionID) > 0 )
		{
			session_id($sessionID);
		}
		session_start();
	}
	
	public function __construct()
	{
		$this->TryToContinue();
	}
}