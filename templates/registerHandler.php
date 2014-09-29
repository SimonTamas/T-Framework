<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 9/21/2014
 * Time: 6:59 PM
 */
require_once( constant_ftpRoot . "/framework/classes/Captcha.php");

class RegisterHandler
{
	
	private $webPage;
	
    // For ids
    const registerForm = "register-form";
    const errorContainer = "error-container";

    const registerButtons = "register-buttons";

    // Form name constants
    const registerUserName = "register-username";
    
    // If first and last name are required
    const registerFirstName = "register-firstname";
    const registerLastName = "register-lastname";
    
    const registerPassName = "register-password";
    const registerPass2Name = "register-password2";
    const registerSentName = "register-sent";
    
    // If adress is required
    const registerLocation = "register-location";
    const registerStreet = "register-street";

    const registerUserPlaceholder = "registerMailHolder";
    const registerFirstNamePlaceholder = "registerFirstNameHolder";
    const registerLastNamePlaceholder = "registerLastNameHolder";
    const registerPassPlaceholder = "registerPasswordHolder";
    const registerPass2Placeholder = "registerPassword2Holder";
    const registerLocationPlaceholder = "registerLocationHolder";
    const registerStreetPlaceholder = "registerStreetHolder";
    // Form settings

    const constant_register_firstNameMinLength = 3;
    const constant_register_firstNameMaxLength = 20;

    const constant_register_lastNameMinLength = 3;
    const constant_register_lastNameMaxLength = 20;

    const constant_register_captchaMinLength = 5;
    const constant_register_captchaMaxLength = 8;

    // Form language keys
    const registerText = "registration";
    const registerUserLabel = "emailLabel";
    const registerFirstNameLabel = "firstNameLabel";
    const registerLastNameLabel = "lastNameLabel";
    const registerPassLabel = "passwordLabel";
    const registerPass2Label = "password2Label";
    const registerLocationLabel = "locationLabel";
    const registerStreetLabel = "streetLabel";
    const registerRememberLabel = "rememberLabel";
    
    const registerAdressText = "transportTitle";

    // For errors
    const constant_register_captchaName = "contact_captcha";
    
    private $requestName;
    private $requestAdress;
    
    
    public function __construct($webPage,$requestName=false,$requestAdress=false)
    {
    	$this->webPage = $webPage;
    	$this->requestName = $requestName;
    	$this->requestAdress = $requestAdress;
    }

    public function CreateForm()
    {

        $formErrors = array();

        $pageLanguage = $this->webPage->GetLanguage()->CurrentLanguage();
        $requiredSpan = new Element("span",array("class"=>"required"),"*");
        $registerForm = new Element("form",array("id"=>self::registerForm,"method"=>"POST"));
        
        $registerTitle = new Element("h1",array(),$this->webPage->GetLanguage()->GetText(self::registerText));
        $registerForm->AddElement($registerTitle);

        $accountLabelText = $this->webPage->GetLanguage()->GetText(self::registerUserLabel);
        $passwordLabelText = $this->webPage->GetLanguage()->GetText(self::registerPassLabel);
        $password2LabelText = $this->webPage->GetLanguage()->GetText(self::registerPass2Label);


        // Account input
        $accountBox = new Element("div",array("id"=>"account-box","class"=>"register-box"));
        $accountLabel = new Element("label",array("for"=>self::registerUserName),$accountLabelText);
        $accountInput = new Element("input",
            array
            (
            	"id" => self::registerUserName,
                "name" => self::registerUserName,
                "type" => "email",
                "required" => "required",
                "placeholder" => $this->webPage->GetLanguage()->GetText(self::registerUserPlaceholder)
            )
        );
        $accountBox->AddElement($accountLabel);
        $accountBox->AddElement($accountInput);

        // Password input
        $passwordBox = new Element("div",array("id"=>"password-box","class"=>"register-box"));
        $passwordLabel = new Element("label",array("for"=>self::registerPassName),$passwordLabelText);
        $passwordInput = new Element("input",
            array
            (
            	"id" => self::registerPassName,
                "name" => self::registerPassName,
                "type" => "password",
                "required" => "required",
                "placeholder" =>$this->webPage->GetLanguage()->GetText(self::registerPassPlaceholder)
            )
        );
        $passwordBox->AddElement($passwordLabel);
        $passwordBox->AddElement($passwordInput);

        // Password repeat input
        $password2Box = new Element("div",array("id"=>"password2-box","class"=>"register-box"));
        $password2Label = new Element("label",array("for"=>self::registerPass2Name),$password2LabelText);
        $password2Input = new Element("input",
            array
            (
            	"id" => self::registerPass2Name,
                "name" => self::registerPass2Name,
                "type" => "password",
                "required" => "required",
                "placeholder" =>$this->webPage->GetLanguage()->GetText(self::registerPass2Placeholder)
            )
        );
        $password2Box->AddElement($password2Label);
        $password2Box->AddElement($password2Input);

        // Captcha
        $captcha = new Captcha($this->webPage);
        $captchaDiv = $captcha->CreateCaptcha(self::constant_register_captchaMinLength);

        // Register Button
        $submitBox = new Element("div",array("id"=>"submit-box","class"=>"register-box"));
        $submitButton = new Element("button",array("id"=>"sendForm","name"=>"registerSend","class"=>array("button","textshadow")),$this->webPage->GetLanguage()->GetText("register"));
		$submitBox->AddElement($submitButton);
        
        // START ADDING BOXES;
        $registerForm->AddElement($accountBox);
        if ( $this->requestName )
        {
        	 // First Name
        	$nameBox = new Element("div",array("class"=>"register-box"));
        	
        	$firstNameBox = new Element("div",array("id"=>"first-name","class"=>array("register-box","half")));
        	$firstNameLabelText = $this->webPage->GetLanguage()->GetText(self::registerFirstNameLabel);
        	$firstNameLabel = new Element("label",array("for"=>self::registerFirstName),$firstNameLabelText);
        	$firstNameInput = new Element("input",
        		array
        		(
        			"id" => self::registerFirstName,
        			"name" => self::registerFirstName,
        			"required" => "required",
        			"placeholder" => $this->webPage->GetLanguage()->GetText(self::registerFirstNamePlaceholder)
        		)
        	);
        	$firstNameBox->AddElement($firstNameLabel);
        	$firstNameBox->AddElement($firstNameInput);
        	$nameBox->AddElement($firstNameBox);
        	
        	// Last Name
        	$lastNameBox = new Element("div",array("id"=>"last-name","class"=>array("register-box","half")));
        	$lastNameLabelText = $this->webPage->GetLanguage()->GetText(self::registerLastNameLabel);
        	$lastNameLabel = new Element("label",array("for"=>self::registerLastName),$lastNameLabelText);
        	$lastNameInput = new Element("input",
        		array
        		(
        			"id" => self::registerLastName,
        			"name" => self::registerLastName,
        			"required" => "required",
        			"placeholder" => $this->webPage->GetLanguage()->GetText(self::registerLastNamePlaceholder)
        		)
        	);
        	$lastNameBox->AddElement($lastNameLabel);
        	$lastNameBox->AddElement($lastNameInput);
        	$nameBox->AddElement($lastNameBox);
        	
        	$registerForm->AddElement($nameBox);
        }

        $registerForm->AddElement($passwordBox);
        $registerForm->AddElement($password2Box);
        
        if ( $this->requestAdress )
        {
        	$adressText = $this->webPage->GetLanguage()->GetText(self::registerAdressText);
        	$adressTitle = new Element("h2",array(),$adressText);
        	
        	$adressBox1 = new Element("div",array("class"=>"register-box"));
        	// City
        	$locationBox = new Element("div",array("id"=>"location-box","class"=>array("register-box","half")));
        	$locationLabelText = $this->webPage->GetLanguage()->GetText(self::registerLocationLabel);
        	$locationLabel = new Element("label",array("for"=>self::registerLocation),$locationLabelText);
        	$locationInput = new Element("input",
        		array
        		(
        			"id" => self::registerLocation,
        			"name" => self::registerLocation,
        			"required" => "required",
        			"placeholder" => $this->webPage->GetLanguage()->GetText(self::registerLocationPlaceholder)
        		)
        	);
        	$locationBox->AddElement($locationLabel);
        	$locationBox->AddElement($locationInput);
        	
        	// Street
        	$streetBox = new Element("div",array("id"=>"street-box","class"=>array("register-box","half")));
        	$streetLabelText = $this->webPage->GetLanguage()->GetText(self::registerStreetLabel);
        	$streetLabel = new Element("label",array("for"=>self::registerStreet),$streetLabelText);
        	$streetInput = new Element("input",
        		array
        		(
        			"id" => self::registerStreet,
        			"name" => self::registerStreet,
        			"required" => "required",
        			"placeholder" => $this->webPage->GetLanguage()->GetText(self::registerStreetPlaceholder)
        		)
        	);
        	$streetBox->AddElement($streetLabel);
        	$streetBox->AddElement($streetInput);
        	
        	$registerForm->AddElement($adressTitle);
        	$adressBox1->AddElement($locationBox);
        	$adressBox1->AddElement($streetBox);
        	$registerForm->AddElement($adressBox1);
        }
        
        //$registerForm->AddElement($captchaDiv);

        $registerForm->AddElement($submitBox);

        return $registerForm;

    }
} 