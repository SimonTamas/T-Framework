<?php

class Form
{
	private $webPage;
	private $identifier;
	private $structure;
	private $httpMethods;
	private $errors;
	
	public function CheckAccount($settings)
	{
		$name = $this->identifier.$settings["name"];
		if ( $this->httpMethods->HasValue($name) )
		{
			$val = $this->httpMethods->GetValue($name);
			$correctFormat = true;
			if ( array_key_exists("type", $settings))
			{
				if ( $settings["type"] == "email" && filter_var($val, FILTER_VALIDATE_EMAIL) == false )
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
							//return "ERROR-USER-LONG";
							return $this->webPage->GetLanguage()->GetText($this->identifier."account-long");
						}
						else if ( $valLen < $min )
						{
							//return "ERROR-USER-SHORT";
							return $this->webPage->GetLanguage()->GetText($this->identifier."account-short");
						}
					}
				}
			}
			if ( $correctFormat )
			{
				$sql = new SqlServer(true);
				$accountExists = $sql->Query($settings["query"]);
				if ( $accountExists && $accountExists->num_rows > 0 )
				{
					//"ERROR-USER-EXISTS";
					return $this->webPage->GetLanguage()->GetText($this->identifier."account-exists");
				}
				else
				{
					return  true;
				}
			}
			else
			{
				//"ERROR-USER-FORMAT";
				return $this->webPage->GetLanguage()->GetText($this->identifier."account-format");
			}
		}
		else
		{
			//"ERROR-USER-NOT-SET";
			return $this->webPage->GetLanguage()->GetText($this->identifier."account-set");
		}
	}
	
	public function CheckPasswords($settings)
	{
		$name = $this->identifier.$settings["name"]."1";
		if ( $this->httpMethods->HasValue($name) )
		{
			$val = $this->httpMethods->GetValue($name);
			if ( array_key_exists("length", $settings) )
			{
				$min = $settings["length"][0];
				$max = $settings["length"][1];
				$valLen = strlen($val);
				if ( $valLen > $max )
				{
					//return "ERROR-PASS-LONG";
					return $this->webPage->GetLanguage()->GetText($this->identifier."password-long");
				}
				else if ( $valLen < $min )
				{
					//return "ERROR-PASS-SHORT";
					return $this->webPage->GetLanguage()->GetText($this->identifier."password-short");
				}
			}
			$name2 = $this->identifier.$settings["name"]."2";
			$val2 = $this->httpMethods->GetValue($name2);
			if ( $val != $val2  )
			{
				//return "ERROR-PASS-MATCH";
				return $this->webPage->GetLanguage()->GetText($this->identifier."password-match");
			}
			return true;
		}
		else
		{
			//return "ERROR-PASS-N0T-SET";
			return $this->webPage->GetLanguage()->GetText($this->identifier."password-set");
		}
	}
	
	public function CheckCaptcha($settings)
	{
		$name = $this->identifier.$settings["name"];
		if ( $this->httpMethods->HasValue($name) )
		{
			$val = $this->httpMethods->GetValue($name);
			if ( Session::Exists($name)  )
			{
				if ( in_array($val,Session::Get($name)) )
				{
					return true;
				}
				else
				{
					//return "ERROR-CAPTCHA-MATCH";
					return $this->webPage->GetLanguage()->GetText($this->identifier."captcha-match");
				}
			}
			else
			{
				// return "ERROR-SESSION-SET"
				return "Framework error - Captcha Session Missing";
			}
		}
		else
		{
			//return "ERROR-CAPTCHA-N0T-SET";
			return $this->webPage->GetLanguage()->GetText($this->identifier."captcha-set");
		}
	}
	
	public function CheckInput($settings)
	{
		$name = $this->identifier.$settings["name"];
		if ( $this->httpMethods->HasValue($name) )
		{
			$val = $this->httpMethods->GetValue($name);
			if ( array_key_exists("length", $settings) )
			{
				$min = $settings["length"][0];
				$max = $settings["length"][1];
				$valLen = strlen($val);
				if ( $valLen > $max )
				{
					return $this->webPage->GetLanguage()->GetText($this->identifier."input-long");
				}
				else if ( $valLen < $min )
				{
					return $this->webPage->GetLanguage()->GetText($this->identifier."input-short");
				}
			}
		}
		else if ( $settings["required"] )
		{
			return $this->webPage->GetLanguage()->GetText($this->identifier."input-set");
		}
		return true;
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
				$groupResult = [];
				for ( $i = 0 ; $i < count($settings) ; $i++ )
				{
					$setting = $settings[$i];
					$partCorrect = $this->CheckPosts($setting);
					$groupResult[$i] = $partCorrect;
				}
				return $groupResult;
				break;
		
			case "account" :
				$settings["name"] = "user";
				return $this->CheckAccount($settings);
				break;
					
			case "password" :
				$settings["name"] = "password";
				return $this->CheckPasswords($settings);
				break;
					
			case "serial" :
				break;
				
			case "captcha" :
				$settings["name"] = "captcha";
				return $this->CheckCaptcha($settings);
				break;
		}
		return true;
	}
	
	public function HasError($key)
	{
		if ( $this->errors )
		{
			if ( array_key_exists($key, $this->errors) )
			{
				return $this->errors[$key];
			}
		}
		return false;
	}
	
	public function GetFormErrors()
	{
		$this->httpMethods = new HttpMethod();
		if ( $this->httpMethods->WasSent($this->identifier."send") )
		{
			$formErrors = [];
			foreach ( $this->structure as $groupName => $groupStructure )
			{
				for( $i = 0 ; $i < count($groupStructure) ; $i++ )
				{
					$structure = $groupStructure[$i];
					$structureState = $this->CheckPosts($structure);
					// If the value isn't true then something 
					// didn't meet the forms requirements !
					
					if ( $structureState !== true )
					{
						if ( array_key_exists("name", $structure)) 
						{
							$formErrors[$structure["name"]] = $structureState;
						}
					}
				}
			}
			return $formErrors;
		}
		return null;
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
        	$settings["langKey"] = $settings["langKey"];
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
		if ( $type != "password" && $this->httpMethods )
		{
			if ( $this->httpMethods->HasValue($name) )
			{
				$inputSettings["value"] = $this->httpMethods->GetValue($name);
			}
		}
		
		// Required is true by default
		if ( !array_key_exists("required", $settings) || ( array_key_exists("required", $settings) && $settings["required"] ) )
		{
			if ( array_key_exists("required", $settings) && $settings["required"] !== true )
			{
				// Required is a number
				$boxLabel->AddElement(new Element("span",array("class"=>"semi-required"),"*"));
			}
			else
			{
				$inputSettings["required"] = "required";
				$boxLabel->AddElement(new Element("span",array("class"=>"required"),"*"));
			}
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
		

		$firstPack = null;
		foreach ( $grouping as $id => $pack )
		{
			
			if ( $firstPack == null )
			{
				$firstPack = $id;
			}
			
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
			
			
			// Remove 6% for padding and margin;
			$percent = floor((($maxLength*100)/$sum)) - 9;
			
			$packSettings = array
			(
				"id" => $name . $id,
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
				$packSettings["required"] = "required 
						1";
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


		$phoneText = $this->webPage->GetLanguage()->GetText($settings["langKey"]."-label");
		$phoneLabel = new Element("label",array("for"=>$name . $firstPack),$phoneText);
		
		
		if ( !array_key_exists("required", $settings) || ( array_key_exists("required", $settings) && $settings["required"] ) )
		{
			$phoneLabel->AddElement(new Element("span",array("class"=>"required"),"*"));
		}
		$phoneBox->AddElement($phoneLabel);
		$phoneBox->AddElement($phoneNumbers);
		return $phoneBox;
	}
	
	private function CreateSendButton($settings)
	{
		$name = "send-button";
		$box = new Element("div",array("id"=>$this->identifier.$name."-box","class"=>$this->identifier."box"));
		
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
	
	private function CreateCaptcha($settings)
	{
		$settings["name"] = $this->identifier."captcha";
		
		// Get Label from DB
		$labelText = $this->webPage->GetLanguage()->GetText($this->identifier."captcha-label");
		
		$box = new Element("div",array("id"=>$settings["name"]."-box","class"=>$this->identifier."box"));
		$boxLabel = new Element("label",array("for"=>$settings["name"]),$labelText);
		$boxLabel->AddElement(new Element("span",array("class"=>"required"),"*"));
		$inputSettings = array
		(
			"id" => $settings["name"],
			"required" => "required",
			"name" => $settings["name"],
			"type" => "text",
			"placeholder" => $this->webPage->GetLanguage()->GetText($this->identifier."captcha-placeholder")
		);
		
		
		$request = "";
		if ( array_key_exists("request", $settings) )
		{
			$request = $settings["request"];
		}
		$captchaImg = new Image(constant_websiteRoot . "captcha".$request,"","",array("id"=>$this->identifier."captcha-img"));
		$captchaButton = new Element("button",array("id"=>$this->identifier."newCaptcha","type"=>"button","class"=>"button"));
		$captchaInner = new Element("div",array("id"=>$this->identifier."captcha-inner","class"=>$this->identifier."box"));
		$captchaInner->AddElement($captchaButton);
		$captchaInner->AddElement($captchaImg);
		
		// Length
		if ( array_key_exists("length", $settings))
		{
			$inputSettings["pattern"] = ".{" . $settings["length"][0] ."," . $settings["length"][1]. "}";
			$inputSettings["title"] = $this->webPage->GetLanguage()->GetTextReplaced($this->identifier."captcha-title",
				array
				(
					$settings["length"][0],
					$settings["length"][1]
				)
			);
		}
		
		$boxInput = new Element("input",$inputSettings);
		
		$containerBox = new Element("div",array("class"=>$this->identifier."box"));
		
		$box->AddElement($boxLabel);
		$box->AddElement($boxInput);
		

		$containerBox->AddElement($captchaInner);
		$containerBox->AddElement($box);
		
		
		
		return $containerBox;
	}
	
	private function CreateBox($settings)
	{
		$type = "input";
		if ( array_key_exists("flag", $settings))
		{
			$type = $settings["flag"];
		}
		else if ( !array_key_exists("name", $settings) )
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
			
			case "captcha" :
				return $this->CreateCaptcha($settings);
			break;
						
			case "account" :
				return $this->CreateInput($settings);
				break;
					
			case "password" :
				$settings["type"] = "password";
				
				$box = new Element("div",array("class"=>$this->identifier."box"));

				$settings["name"] = "password1";
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
	
	public function CreateForm($formErrors)
	{
		$this->errors = $formErrors;
		$form = new Element("form",array("id"=>$this->identifier."form","method"=>$this->method));
		$counter = 0;
		foreach ( $this->structure as $groupName => $groupStructure )
		{
			$titleType = "h3";
			if ( $counter == 0 )
			{
				$titleType = "h2";
			}
			$groupDiv = new Element("div",array("id"=>$this->identifier.$groupName."-container"));
			
			if ( !is_numeric($groupName) )
			{
				$groupTitle = new Element($titleType,array("id"=>$this->identifier.$groupName),$this->webPage->GetLanguage()->GetText($groupName));
				$form->AddElement($groupTitle);
			}
			
			for( $i = 0 ; $i < count($groupStructure) ; $i++ )
			{
				$elem = $this->CreateBox($groupStructure[$i]);				
				if ( $elem )
				{
					if ( array_key_exists("name", $groupStructure[$i]) )
					{
						$error = $this->HasError($groupStructure[$i]["name"]);
						if ( $error !== false )
						{
							// Van hiba
							$errorDiv = new Element("div",array("id"=>$groupStructure[$i]["name"]."Error","class"=>$this->identifier."error"));
							$errorSpan = new Element("span",array(),$error);
							$errorDiv->AddElement($errorSpan);
						
							$elem->AddElement($errorDiv);
						}
					}
					$groupDiv->AddElement($elem);
				}
			}
			$form->AddElement($groupDiv);
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