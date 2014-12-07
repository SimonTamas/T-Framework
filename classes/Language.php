<?php

class Language extends Framework
{

	private $lang;
	private $langElements;
	private static $languageID = 0;
	private $languages;
	
	public function CurrentLanguage()
	{
		return strtolower($this->lang);
	}
	
	public function InitLanguages()
	{
		$sql = new SqlServer(true);
		$checkQuery = $sql->Query("SELECT * FROM language LIMIT 1");
		$numLanguages = $sql->FieldCount($checkQuery);
		for ( $i = 0 ; $i < $numLanguages ; $i++ )
		{
			$fieldName = $sql->FieldName($checkQuery,$i);
			if ( $fieldName != "entry" && $fieldName != "langKey" )
			{
				if ( !in_array($fieldName, $this->languages))
					array_push($this->languages, $fieldName);
			}
		}
		$sql->Disconnect();
	}
	
	public function HasLanguage($lang)
	{
		return in_array($lang,$this->languages);
	}
	
	public function TrySetLanguage($tryLang)
	{
		$sql = new SqlServer(true);
		if ( $this->HasLanguage($tryLang) )
		{
			$this->SetLanguage($tryLang);
		}
	}
	
	public function Languages()
	{
		return $this->languages;
	}
	
	public function GetNumLanguages()
	{
		return count($this->languages);
	}
	
	public function SetElementsLanguage($setTo)
	{
		foreach ( $this->langElements as $elem )
		{
			//$element = $this->langElements[$elem];
			//$element->SetHTML($this->GetText($elem));
		}
	}
	
	public function SetLanguage($setTo)
	{
		$this->lang = $setTo;
		$this->SetElementsLanguage($setTo);
	}
	
	public static function GetTextForLanguage($key,$language)
	{
		$sql = new SqlServer(true);
		$langQuery = $sql->Query("SELECT " . $language . " FROM language WHERE langKey = '$key'");
		$sql->Disconnect();
		if ( $sql->NumRows($langQuery) > 0 )
		{
			return mysql_real_escape_string($sql->Result($langQuery));
		}
		else
		{
			return "?";
		}
	}
	
	public function GetText($key,$elem=NULL,$requestLanguage=NULL)
	{
		$sql = new SqlServer(true);
		$langColumn = $this->lang;
		if ( $requestLanguage != NULL )
		{
			$langColumn = $requestLanguage;
		}
		$langQuery = $sql->Query("SELECT " . $langColumn . " FROM language WHERE langKey = '$key'");
		$sql->Disconnect();
		if ( $sql->NumRows($langQuery) > 0 )
		{
			if ( $elem )
			{
				$this->langElements[$key] = $elem;
			}
			return mysql_real_escape_string($sql->Result($langQuery));
		}
		else
		{
			return "?";
		}
	}
	
	public function GetTextReplaced($key,$replaceData,$elem=NULL,$requestLanguage=NULL)
	{
		$normalText = $this->GetText($key,$elem,$requestLanguage);
		if ( !is_array($replaceData) )
		{
			$replaceData = array($replaceData);
		}
		for ( $i = 0 ; $i < count($replaceData) ; $i++ )
		{
			$normalText = preg_replace("/!".$i."/",$replaceData[$i],$normalText);
		}
		return $normalText;
	}
	
	public function CheckLocation()
	{
		$ip = $_SERVER['REMOTE_ADDR'];
		$details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
		if ( property_exists($details,"country") && $details->country != $this->lang )
		{	
			$this->TrySetLanguage($details->country);
		}
	}

	public function __construct($setLang=constant_defaultLanguage)
	{
		$this->languages = array();
		$this->InitLanguages();
		
		$this->lang = $setLang;
		$this->langElements = array();
		//$this->CheckLocation();
		
		self::$languageID++;
		
		// Create an instance of a debugger for the language instance
		//$this->debugger = new Debugger(parent::$debugFramework,"language" . self::$languageID);
	}
}

?>