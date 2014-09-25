<?php

class Captcha
{
    private $webPage;

    public function __construct($webPage)
    {
        $this->webPage = $webPage;
    }

    public function CreateCaptcha($minLength)
    {
        $requiredSpan = new Element("span",array("class"=>"required"),"*");
        $securityCodeString = $this->webPage->GetLanguage()->GetText("securityCode");

        $captchaLabel = new Element("label",array(),$securityCodeString.":");
        $captchaLabel->AddElement($requiredSpan);

        $captchaImg = new Image(constant_websiteRoot . "captcha",$securityCodeString,$securityCodeString,array("id"=>"captchaImg"));
        $captchaButton = new Element("button",array("id"=>"request-newCaptcha","type"=>"button"));
        $captchaButtonContainer = new Element("div",array("id"=>"request-newCaptcha-container"));
        $captchaButtonContainer->AddElement($captchaButton);
        $capcheInput = new Element("input",
            array
            (
                "id" => "captchaInput",
                "class" => "shadowM",
                "autocomplete" => "off",
                "minlength" => $minLength,
                "name" => "contact_captcha",
                "required" => "required",
                "placeholder" => $this->webPage->GetLanguage()->GetText("contactCaptchaHolder")
            )
        );

        $captchaDiv = new Element("div",array("id"=>"captcha-box","class"=>"contact-box"));
        $captchaDiv->AddElement($captchaLabel);

        $captchaInner = new Element("div",array("id"=>"captcha-inner"));
        $captchaInner->AddElement($captchaButtonContainer);
        $captchaInner->AddElement($captchaImg);
        $captchaInner->AddElement($capcheInput);
        $captchaDiv->AddElement($captchaInner);

        return $captchaDiv;
    }
}





if ( array_key_exists(constant_register_captchaName,$formErrors) )
{
    $captchaError = new Element("div",array("class"=>"contact-error"));
    $captchaErrorString = new Element("span",array(),$formErrors[constant_contact_captchaName]);
    $captchaError->AddElement($captchaErrorString);
    $captchaDiv->AddElement($captchaError);
}