<?php

class LoginHandler
{
    // For ids
    const loginForm = "login-form";
    const rememberContainer = "remember-container";
    const errorContainer = "error-container";

    const loginButtons = "login-buttons";

    // Form name constants
	const loginUserName = "login-username";
	const loginPassName = "login-password";
	const loginSentName = "login-sent";
	const loginRememberName = "login-remember";

    // Form language keys
    const loginUserLabel = "emailLabel";
    const loginPassLabel = "passwordLabel";
    const loginRememberLabel = "rememberLabel";

    const loginUserPlaceholder = "contactMailHolder";
    const loginPassPlaceholder = "passwordHolder";

    // Login state constants
    const loginEmpty = "loginEmpty";
    const loginUserNotDefined = "userNotDefined";
    const loginPasswordNotDefined = "passNotDefined";
    const loginBothNotDefined = "bothNotDefined";
    const loginSuccess= "loginSuccess";
    const loginIncorrect = "loginIncorrect";

    public static function CheckCredentials($user,$pass)
    {
        $sql = new SqlServer();
        $checkQuery = $sql->Query("SELECT entry FROM accounts WHERE accountString = '$user' AND accountPassword = '$pass'");
        $result = $sql->NumRows($checkQuery) > 0;
        $sql->Disconnect();
        $sql = null;
        return $result;
    }

	public static function GetState()
	{
		$httpMethods = new HttpMethod();
        $logging = false;
		$user = null;
		$pass = null;
		$remember = false;
        $loginState = self::loginEmpty;

		if ( $httpMethods->HasValue(self::loginSentName) )
		{
            // Form was sent!
            $logging = true;
			$user = $httpMethods->GetValue(self::loginUserName);
			$pass = $httpMethods->GetValue(self::loginPassName);

            // Encrypt password
			//$pass = password_hash($pass, PASSWORD_BCRYPT);
		}
		else if ( Session::Exists(self::loginUserName) && Session::Exists(self::loginPassName) )
		{
			$user = Session::Get(self::loginUserName);
			$pass = Session::Get(self::loginPassName);
		}

        // Check DB
        if ( $user && $pass )
        {
            $loginGood = self::CheckCredentials($user,$pass);
            if ( $loginGood )
            {
                if ( $logging )
                {
                    Session::Set(self::loginUserName,$user);
                    Session::Set(self::loginPassName,$pass);
                    $remember = $httpMethods->GetValue(self::loginRememberName);
                    if ( $remember )
                    {
                        Cookie::Set("PHPSESSID",session_id());
                    }
                }
                $loginState = self::loginSuccess;
            }
            else if ( $logging )
            {
                $loginState = self::loginIncorrect;
            }
        }
        else if ( $logging  )
        {
            if ( isset($user) )
            {
                // Password is not defined
                $loginState = self::loginPasswordNotDefined;
            }
            else if ( isset($pass) )
            {
                // User is not defined
                $loginState = self::loginUserNotDefined;
            }
            else
            {
                // User and password are not defined
                $loginState = self::loginBothNotDefined;
            }
        }
        return $loginState;
	}
	
	public static function CreateForm($webPage,$loginState)
	{
		$pageLanguage = $webPage->GetLanguage()->CurrentLanguage();
		$loginForm = new Element("form",array("id"=>self::loginForm,"method"=>"POST"));
		

        $accountLabelText = $webPage->GetLanguage()->GetText(self::loginUserLabel);
        $passwordLabelText = $webPage->GetLanguage()->GetText(self::loginPassLabel);
        $rememberLabelText = $webPage->GetLanguage()->GetText(self::loginRememberLabel);

        $loginText = $webPage->GetLanguage()->GetText("login");
        $registerText = $webPage->GetLanguage()->GetText("register");

        switch($loginState)
        {
            case self::loginIncorrect :
                $accountLabelText .= $webPage->GetLanguage()->GetText($loginState);
            break;

            case self::loginUserNotDefined :
                // In case html5 is not supported
                $accountLabelText .= $webPage->GetLanguage()->GetText($loginState);
            break;

            case self::loginBothNotDefined :
                // In case html5 is not supported
                $accountLabelText .= $webPage->GetLanguage()->GetText($loginState);
            break;
        }

        // Account input
        $accountLabel = new Element("label",array("for"=>self::loginUserName),$accountLabelText);
        $accountInput = new Element("input",
            array
            (
                "name" => self::loginUserName,
                "type" => "email",
                "required" => "required",
                "placeholder" => $webPage->GetLanguage()->GetText(self::loginUserPlaceholder)
            )
        );
		
		// Password input
		$passwordLabel = new Element("label",array("for"=>self::loginPassName),$passwordLabelText);
		$passwordInput = new Element("input",
				array
				(
						"name" => self::loginPassName,
						"type" => "password",
						"required" => "required",
						"placeholder" =>$webPage->GetLanguage()->GetText(self::loginPassPlaceholder)
				)
		);
		
		// Remember me checkbox
		$rememberContainer = new Element("div",array("id"=>self::rememberContainer));
		$rememberLabel = new Element("label",array("for"=>self::loginRememberName),$rememberLabelText);
		$rememberInput = new Element("input",
				array
				(
						"name" => self::loginRememberName,
						"type" => "checkbox"
				)
		);

		$rememberContainer->AddElement($rememberLabel);
		$rememberContainer->AddElement($rememberInput);

        if ( $loginState == self::loginIncorrect )
        {
            $errorContainer = new Element("div",array("id"=>self::errorContainer));
        }
		
		// Login button
		$loginButtonsContainer = new Element("div",array("id"=>self::loginButtons));
		$loginButton = new Element("button",array("class"=>"button","name"=>self::loginSentName),$loginText);

        // Register anchor
        $registerPage = $webPage->GetLanguage()->GetText("registerPage");
        $registerURL = constant_websiteRoot.$pageLanguage."/".$registerPage;
		$registerAnchor = new Element("a",array("href"=>$registerURL,"class"=>"button"),$registerText);
		
		$loginButtonsContainer->AddElement($registerAnchor);
		$loginButtonsContainer->AddElement($loginButton);
		
		// Add elements to form
        $loginForm->AddElement($accountLabel);
        $loginForm->AddElement($accountInput);
        $loginForm->AddElement($passwordLabel);
        $loginForm->AddElement($passwordInput);
        $loginForm->AddElement($rememberContainer);
        $loginForm->AddElement($loginButtonsContainer);
		
		return $loginForm;
	}
}