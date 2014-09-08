<?php

class Website
{
	private $session;
	private $pages;
	
	public function AddPage(WebPage $page)
	{
		$this->pages[$page->PageName()] = $page;
	}
	
	
	public function GetPageHTML($pageName)
	{
		if ( array_key_exists($pageName,$this->pages) )
		{
			return $this->pages[$pageName]->GetHTML();
		}
		return "";
	}
	
	public function Session()
	{
		return $this->session;
	}
	
	
	public function __construct()
	{
		$this->pages = array();
		$this->session = new Session();
		
		$clientAdress = $_SERVER['REMOTE_ADDR'];
		
		$this->session->start_session($clientAdress, true);
	}
}


?>