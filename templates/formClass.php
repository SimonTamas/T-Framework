<?php

class Form
{
	private $webPage;
	private $identifier;
	private $structure;
	private $httpMethods;
	
	public function CheckAccount($settings)
	{
		$name = $this->identifier.$settings["name"];
		if ( $this->httpMethods->HasValue($name) )
		{
			$val = $this->httpMethods->GetValue($name);
			$correctFormat = true;
			if ( array_key_exists("type", $settings))
			{
				if ( $settings["type"] == "email" && filter_var($val, FILTER_VALIDATE_EMAIL) )
				{
					$correctFormat = false;
				}
				else 
				{
					if ( array_key_exists("length", $settings) )
					{
						$min = $settings["length"][0];
						$max = $settings["length"][1];
						$valLen = strlen($val);
						if ( $valLen > $max )
						{
							return "ERROR-USER-LONG";
						}
						else if ( $valLen < $min )
						{
							return "ERROR-USER-SHORT";
						}
					}
				}
			}
			if ( $correctFormat )
			{
				$sql = new SqlServer(true);
				$accountExistsSTMT = $sql->Prepare("SELECT COUNT() FROM accounts WHERE user = ?",$user);
				if ( $accountExistsSTMT->num_rows > 0 )
				{
					// Account exists
					return "ERROR-USER-EXISTS";
				}
				else
				{
					return  true;
				}
			}
			else
			{
				return "ERROR-USER-FORMAT";
			}
		}
		else
		{
			"ERROR-USER-NOT-SET";
		}
	}
	
	public function CheckPasswords($settings)
	{
		
	}
	
	public function CheckInput($settings)
	{
		
	}
	
	public function CheckPosts($settings)
	{
		$type = "input";
		if ( array_key_exists("flag", $settings))
		{
			$type = $settings["flag"];
		}
		else if ( !array_key_exists("name",$settings) )
		{
			$type = "group";
		}
		switch($type)
		{
			case "input" :
				return $this->CheckInput($settings);
				break;
					
			case "group" :
				for ( $i = 0 ; $i < count($settings) ; $i++ )
				{
					$setting = $settings[$i];
					$this->CheckPosts($setting);
				}
				break;
		
			case "account" :
				$settings["name"] = "user";
				return $this->CheckAccount($settings);
				break;
					
			case "password" :
				$this->CheckPasswords($settings);
				break;
					
			case "serial" :
				break;
		}
	}
	
	public function GetState()
	{
		$this->httpMethods = new HttpMethod();
		if ( $this->httpMethods->HasValue($this->identifier."send") )
		{
			foreach ( $this->structure as $groupName => $groupStructure )
			{
				for( $i = 0 ; $i < count($groupStructure) ; $i++ )
				{
					$structure = $groupStructure[$i];
					$structureCorrect = $this->CheckPosts($structure);
				}
			}
		}
		return false;
	}
	
	public function CreateInput($settings)
	{
		$name = $this->identifier.$settings["name"];
        $type = "text";
        if ( array_key_exists("type", $settings) )
        {
        	$type = $settings["type"];
        }
		

        if ( !array_key_exists("langKey", $settings) )
        {
        	$settings["langKey"] = $name;
        }
        else
        {
        	$settings["langKey"] = $this->identifier . $settings["langKey"];
        }
        
        // Get Label from DB
        $labelText = $this->webPage->GetLanguage()->GetText($settings["langKey"]."-label");
        
		$box = new Element("div",array("id"=>$name."-box","class"=>$this->identifier."box"));
		$boxLabel = new Element("label",array("for"=>$name),$labelText);
		
		$inputSettings = array
		(
			"id" => $name,
			"name" => $name,
			"type" => $type,
			"placeholder" => $this->webPage->GetLanguage()->GetText($settings["langKey"]."-placeholder")
		);
		
		// If method exists check for value
		if ( $this->httpMethods )
		{
			if ( $this->httpMethods->HasValue($name) )
			{
				$inputSettings["value"] = $this->httpMethods->GetValue($name);
			}
		}
		
		// Required is true by default
		if ( !array_key_exists("required", $settings) || ( array_key_exists("required", $settings) && $settings["required"] ) )
		{
			$inputSettings["required"] = "required";
			$boxLabel->AddElement(new Element("span",array("class"=>"required"),"*"));
		}
		
		if ( $type == "number" )
		{
			if ( array_key_exists("min", $settings) )
			{
				$inputSettings["min"] = $settings["min"];
			}
		}
		
		// Length
		if ( array_key_exists("length", $settings))
		{
			$inputSettings["pattern"] = ".{" . $settings["length"][0] ."," . $settings["length"][1]. "}";
			$inputSettings["title"] = $this->webPage->GetLanguage()->GetTextReplaced($settings["langKey"]."-title",
				array
				(
					$settings["length"][0],
					$settings["length"][1]
				)
			);
		}
		// Autocomplete
		if ( array_key_exists("autocomplete",$settings) && $settings["autocomplete"] )
		{
			$inputSettings["autocomplete"] = "on";
		}
		else
		{
			$inputSettings["autocomplete"] = "off";
		}
		
		$boxInput = new Element("input",$inputSettings);
		
		// Error ?
		
		$box->AddElement($boxLabel);
		$box->AddElement($boxInput);
		return $box;
	}
	
	private function CreateSerialInput($settings)
	{
		$name = $this->identifier.$settings["name"];
		$phoneBox = new Element("div",array("id"=>$name."-box","class"=>$this->identifier."box"));
		

		if ( !array_key_exists("langKey", $settings) )
        {
        	$settings["langKey"] = $name;
        }
        else
        {
        	$settings["langKey"] = $this->identifier . $settings["langKey"];
        }
		
		
		$phoneText = $this->webPage->GetLanguage()->GetText($settings["langKey"]."-label");
		$phoneLabel = new Element("label",array("for"=>$name),$phoneText);
		
		
		
		if ( !array_key_exists("required", $settings) || ( array_key_exists("required", $settings) && $settings["required"] ) )
		{
			$phoneLabel->AddElement(new Element("span",array("class"=>"required"),"*"));
		}
		$phoneBox->AddElement($phoneLabel);
		
		$phoneNumbers = new Element("div",array("id"=>$name."-container"));
		$grouping = $settings["group"];
		$sum = 0;
		foreach ( $grouping as $i => $pack )
		{
			if ( is_array($pack) )
			{
				$sum += $pack[1];
			}
			else if ( is_string($pack))
			{
				$sum += strlen($pack);
			}
			else
			{
				$sum += $pack;
			}
		}
		foreach ( $grouping as $id => $pack )
		{
			$groupPattern = $maxLength = $pack;
			if ( is_array($groupPattern) )
			{
				$maxLength = $groupPattern[1];
				$groupPattern = $groupPattern[0] . ",". $groupPattern[1];
			}
			else if ( is_string($groupPattern) )
			{
				$maxLength = $groupPattern = strlen($groupPattern);
			}
			
			
			// Remove 10% for padding and margin;
			$percent = floor((($maxLength*100)/$sum)) - 10;
			
			
			$packSettings = array
			(
				"id" => $name .  $id,
				"style" => "width:" . $percent  . "%",
				"data-width" => $maxLength
			);
			
			
			// Add class if pack is of type provider
			if ( is_string($id) )
			{
				$packSettings["class"] = $id;
			}
			
			
			// Predefined packs dont need requirements!
			if ( is_string($pack) )
			{
				$packSettings["value"] = $pack;
				$packSettings["disabled"] = "disabled";
			}
			else 
			{
				// Send out name to be given back;
				$packSettings["name"] = $name . $id;
				

				if ( $this->httpMethods )
				{
					if ( $this->httpMethods->HasValue($packSettings["name"]) )
					{
						$packSettings["value"] = $this->httpMethods->GetValue($packSettings["name"]);
					}
				}
				
				if ( !array_key_exists("required", $settings) || ( array_key_exists("required", $settings) && $settings["required"] ) )
				{
					$packSettings["required"] = "required";
					$packSettings["maxlength"] = $maxLength;
					$packSettings["pattern"] = "[0-9]{" . $groupPattern . "}";
				}
			}
			$phoneInputPack = new Element("input",$packSettings);
			$phoneNumbers->AddElement($phoneInputPack);
		}
		$phoneBox->AddElement($phoneNumbers);
		return $phoneBox;
	}
	
	private function CreateSendButton($settings)
	{
		$name = "send-button";
		$box = new Element("div",array("id"=>$name."-box","class"=>$this->identifier."box"));
		
		$buttonText = $this->webPage->GetLanguage()->GetText($this->identifier."title");
		$button = new Element("button",array("name"=>$this->identifier."send"),$buttonText);
		if ( array_key_exists("class", $settings) )
		{
			$class = new ElementProperty("class",$settings["class"]);
			$button->ElementProperties()->AddProperty($class);
		}
		$box->AddElement($button);
		return $box;
	}
	
	private function CreateBox($settings)
	{
		$type = "input";
		if ( array_key_exists("flag", $settings))
		{
			$type = $settings["flag"];
		}
		else if ( !array_key_exists("name",$settings) )
		{
			$type = "group";
		}
		switch($type)
		{
			case "input" :
				return $this->CreateInput($settings);
				break;
					
			case "group" :
				$box = new Element("div",array("class"=>$this->identifier."box"));
				for ( $i = 0 ; $i < count($settings) ; $i++ )
				{
					$elem = $this->CreateBox($settings[$i]);
					if ( $elem )
					{
						$box->AddElement($elem);
					}
				}
				return $box;
			break;
						
			case "account" :
				$settings["name"] = "user";
				return $this->CreateInput($settings);
				break;
					
			case "password" :
				$settings["type"] = "password";
				$settings["name"] = "password1";
				$box = new Element("div",array("class"=>$this->identifier."box"));
				$box->AddElement($this->CreateInput($settings));
				$settings["name"] = "password2";
				$box->AddElement($this->CreateInput($settings));
				return $box;
				break;
					
			case "serial" :
				return $this->CreateSerialInput($settings);
			    break;
			case "send" :
				return $this->CreateSendButton($settings);
			break;
		}
	}
	
	public function CreateForm()
	{
		$form = new Element("form",array("id"=>$this->identifier."form","method"=>$this->method));
		$counter = 0;
		foreach ( $this->structure as $groupName => $groupStructure )
		{
			$titleType = "h3";
			if ( $counter == 0 )
			{
				$titleType = "h2";
			}
			$groupTitle = new Element($titleType,array(),$this->webPage->GetLanguage()->GetText($groupName));
			$form->AddElement($groupTitle);
			for( $i = 0 ; $i < count($groupStructure) ; $i++ )
			{
				$elem = $this->CreateBox($groupStructure[$i]);
				if ( $elem )
				{
					$form->AddElement($elem);
				}
			}
			$counter++;
		}
		return $form;
	}
	
	public function __construct($webPage,$method,$identifier,$structure=array())
	{
		$this->webPage = $webPage;
		$this->identifier = $identifier;
		$this->structure = $structure;
		$this->method = $method;
	}
}