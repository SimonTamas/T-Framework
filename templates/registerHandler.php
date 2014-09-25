<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 9/21/2014
 * Time: 6:59 PM
 */
require_once("../classes/Captcha.php");

class RegisterHandler
{

    // For ids
    const registerForm = "register-form";
    const errorContainer = "error-container";

    const registerButtons = "register-buttons";

    // Form name constants
    const registerUserName = "register-username";
    const registerPassName = "register-password";
    const registerPass2Name = "register-password2";
    const registerSentName = "register-sent";

    const registerUserPlaceholder = "contactMailHolder";
    const registerPassPlaceholder = "passwordHolder";
    const registerPass2Placeholder = "password2Holder";

    // Form settings

    const constant_register_firstNameMinLength = 3;
    const constant_register_firstNameMaxLength = 20;

    const constant_register_lastNameMinLength = 3;
    const constant_register_lastNameMaxLength = 20;

    const constant_register_captchaMinLength = 5;
    const constant_register_captchaMaxLength = 8;

    // Form language keys
    const registerUserLabel = "emailLabel";
    const registerPassLabel = "passwordLabel";
    const registerPass2Label = "password2Label";
    const registerRememberLabel = "rememberLabel";

    // For errors
    const constant_register_captchaName = "contact_captcha";

    public static function CreateForm($webPage)
    {

        $formErrors = array();

        $pageLanguage = $webPage->GetLanguage()->CurrentLanguage();
        $requiredSpan = new Element("span",array("class"=>"required"),"*");
        $registerForm = new Element("form",array("id"=>self::registerForm,"method"=>"POST"));

        $accountLabelText = $webPage->GetLanguage()->GetText(self::registerUserLabel);
        $passwordLabelText = $webPage->GetLanguage()->GetText(self::registerPassLabel);
        $password2LabelText = $webPage->GetLanguage()->GetText(self::registerPass2Label);


        // Account input
        $accountLabel = new Element("label",array("for"=>self::registerUserName),$accountLabelText);
        $accountInput = new Element("input",
            array
            (
                "name" => self::registerUserName,
                "type" => "email",
                "required" => "required",
                "placeholder" => $webPage->GetLanguage()->GetText(self::registerUserPlaceholder)
            )
        );

        // Password input
        $passwordLabel = new Element("label",array("for"=>self::registerPassName),$passwordLabelText);
        $passwordInput = new Element("input",
            array
            (
                "name" => self::registerPassName,
                "type" => "password",
                "required" => "required",
                "placeholder" =>$webPage->GetLanguage()->GetText(self::registerPassPlaceholder)
            )
        );

        // Password repeat input
        $password2Label = new Element("label",array("for"=>self::registerPass2Name),$password2LabelText);
        $password2Input = new Element("input",
            array
            (
                "name" => self::registerPass2Name,
                "type" => "password",
                "required" => "required",
                "placeholder" =>$webPage->GetLanguage()->GetText(self::registerPass2Placeholder)
            )
        );

        $captcha = new Captcha(constant_register_captchaMinLength);

        $registerForm->AddElement($accountLabel);
        $registerForm->AddElement($accountInput);
        $registerForm->AddElement($passwordLabel);
        $registerForm->AddElement($passwordInput);
        $registerForm->AddElement($password2Label);
        $registerForm->AddElement($password2Input);
        $registerForm->AddElement($captcha);

        return $registerForm;

    }
} 