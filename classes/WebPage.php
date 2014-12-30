<?php

class WebPage
{
	private $html; 
	private $language;
	private $title;
	private $pageName;
	private $session;
	private $obfuscatePage;
	private $httpMethod;
	private $sql;
	
	private static $_instance = NULL;

    public static function getInstance() 
    {
        return self::$_instance;
    }
    
    public function IsLogd($key)
    {
    	return $this->session->Exists($key);
    }
    
    public function GetSQL()
    {
    	return $this->sql;
    }
   
    public function ReturnHome()
    {
    	header('Location: '.str_replace("index.php","",$_SERVER['PHP_SELF']));
    	die;
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
	
	public function GetHttpMethod()
	{
		if ( $this->httpMethod == null )
		{
			$this->httpMethod = new HttpMethod();
		}
		return $this->httpMethod;
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
		//Debugger::SetDocument($pageName);
		$this->language = new Language($pageLanguage);
		if ( !$this->language->HasLanguage($pageLanguage))
		{
			// Maybe he wanted the page
			if ( ElementsLoader::PageExists($this,$pageLanguage) )
			{
				$this->language = new Language(constant_defaultLanguage);
				$pageName = $pageLanguage;
			}
			else
			{
				if ( Session::Exists("language") )
				{
					Session::Destroy("language");
				}
				$this->ReturnHome();
			}
		}
		
		// Since the pageName can be in different languages 
		// we need to get the key from that pageName
		
		// Set pageName to be sure
		$this->pageName = $pageName;
		$this->sql = new SqlServer(true);
		$this->session = new Session();
		
		
		$languages = $this->GetLanguage()->Languages();
		foreach ( $languages as $language )
		{
			$queryString = "SELECT langKey FROM language WHERE " . $language . " = '" . $pageName . "'";
			$pageKeyQuery = $this->sql->Query($queryString);
			if ( $this->sql->NumRows($pageKeyQuery) > 0 )
			{
				// pageNames are enlisted as xyzPage (langKey) so remove the Page from the end
				$this->pageName = str_replace("Page", "", $this->sql->Result($pageKeyQuery));
				break;
			}
		}
		
		
		
		
		/* ------  Let's construct a template --------
		----------------------------------------------*/
		
		// A webpage starts with the html tag
		$this->html = new Element("html",array("lang"=>$this->language->CurrentLanguage()));
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