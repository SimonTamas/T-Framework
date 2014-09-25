<?php


require_once("core.php");
require_once("templates/loginHandler.php");
require_once("default_settings/constants.php");

$defaultPage = "home";
$defaultLang = "eng";

$obfuscatePage = false;
$inlineHeadElements = false;

if ( isset($_GET['page']) )
{
	$defaultPage = $_GET['page'];
}
if ( isset($_GET['lang']) )
{
	$defaultLang = strtoupper($_GET['lang']);
	if ( !Session::Exists("language") )
	{
		Session::Set("language",$defaultLang);
	}
}
else if ( Session::Exists("language") ) 
{
	$defaultLang = Session::Get("language");
}

Framework::SetObfuscate($obfuscatePage);
Framework::SetInlineHead($inlineHeadElements);

$thisPage = new WebPage($defaultPage,$defaultLang);
echo $thisPage->GetHTML($obfuscatePage);


?>