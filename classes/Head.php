<?php

class Head extends Element
{
	private $title;
	
	public function GetTitle()
	{
		return $this->title->GetHTML();
	}
	
	public function SetTitle($setTo)
	{
		$this->title->SetHTML($setTo);
	}
	

	public function Html()
	{
		return parent::Html() . $this->GetTitle();
	}
	
	public function __construct()
	{
		parent::__construct("head");
		$this->title = new Element("title");
	}
}