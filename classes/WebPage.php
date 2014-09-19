<?php

class WebPage
{
	private $html; 
	private $language;
	private $title;
	private $pageName;
	private $session;
	private $obfuscatePage;
	
	private static $_instance = NULL;

    public static function getInstance() 
    {
        return self::$_instance;
    }
    
    public function PreLoadElements($type,$id="")
    {
    	// Preload images
    	$elems = $this->html->GetChildElements($type,$id);
    	$preloadJavascript = new Element("script");
    	if ( $type == "img" )
    	{
	    	for ( $i = 0 ; $i < count($elems) ; $i++ )
	    	{
	    		$img = $elems[$i];
				$preloadJavascript->AppendHTML("Image$i = new Image() Image$i.src = \"" . $img->GetPropertyValue("src") . "\" ");
	    	}
	    }
	    return $preloadJavascript;
    }
	
	public function Html()
	{
		return $this->html;
	}
	
	public function Session()
	{
		return $this->session;
	}
	
	public function GetHTML($obfuscated=false)
	{
		$html = "<!DOCTYPE html>" . $this->html->GetHTML($obfuscated);
		return $html;
		//return Framework::Minify($html);
	}
	
	public function SetTitle($setTo)
	{
		$this->title = $setTo;
		$this->Html()->FindChildElement("head")->SetTitle($setTo);
	}
	
	public function GetTitle()
	{
		return $this->title;
	}
	
	public function GetLanguage()
	{
		return $this->language;
	}
	
	public function SetLanguage($setTo)
	{
		$this->language->SetLanguage($setTo);
	}
	
	public function PageName()
	{
		return $this->pageName;
	}

	public function __construct($pageName,$pageLanguage=constant_defaultLanguage,$loadDefaults=true)
	{
		self::$_instance = $this;
		Debugger::SetDocument($pageName);
		$this->language = new Language($pageLanguage);
		// Since the pageName can be in different languages 
		// we need to get the key from that pageName
		
		// Set pageName to be sure
		$this->pageName = $pageName;
		
		// Create session
		$this->session = new Session();
		
		$sql = new SqlServer(true);
		$languages = $this->GetLanguage()->Languages();
		foreach ( $languages as $language )
		{
			$queryString = "SELECT langKey FROM language WHERE " . $language . " = '" . $pageName . "'";
			$pageKeyQuery = $sql->Query($queryString);
			if ( $sql->NumRows($pageKeyQuery) > 0 )
			{
				// pageNames are enlisted as xyzPage (langKey) so remove the Page from the end
				$this->pageName = str_replace("Page", "", $sql->Result($pageKeyQuery));
				break;
			}
		}
		$sql->Disconnect();
		
		
		
		
		/* ------  Let's construct a template --------
		----------------------------------------------*/
		
		// A webpage starts with the html tag
		$this->html = new Element("html");
		//
		// 
		//
		// ------------------------- HEAD -------------------------	
		$head = new Head();
		$this->html->AddElement($head);
		if ( $loadDefaults )
		{
			ElementsLoader::LoadElementsFrom($this,"default","head");
		}
		ElementsLoader::LoadElementsFrom($this,$this->PageName(),"head");
		// --------------------------------------------------------
		//
		//
		//
		// ------------------------- BODY -------------------------	
		$body = new Element("body");
		$this->html->AddElement($body);
		// --------------------------------------------------------
		//
		//
		//
		// ------------------------ WRAPPER -----------------------
		$wrapper = new Element("div",array( "id" => "wrapper" ));
		$body->AddElement($wrapper);
		
		$header = new Element("header");
		$wrapper->AddElement($header);
		if ( $loadDefaults )
		{
			ElementsLoader::LoadElementsFrom($this,"default","header");
		}
		
		$content = new Element("div",array( "id" => "content" ));
		$wrapper->AddElement($content);
		ElementsLoader::LoadElementsFrom($this,$this->PageName(),"content");
		// --------------------------------------------------------
		//
		//
		//
		// ----------------------- FOOTER -------------------------	
		$footer = new Element("footer");
		$body->AddElement($footer);
		if ( $loadDefaults )
		{
			ElementsLoader::LoadElementsFrom($this,"default","footer");
		}
		// --------------------------------------------------------
		
	}
}



?>