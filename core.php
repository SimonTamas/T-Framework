<?php

// These are default settings
require_once("default_settings/constants.php");
require_once("default_settings/localization.php");

require_once("classes/Session.php");
require_once("classes/Cookie.php");
require_once("classes/ClosureCompiler.php");
require_once("classes/Framework.php");
require_once("classes/Obfuscator.php");
require_once("classes/Cacher.php");
require_once("classes/Functions.php");
require_once("classes/SqlServer.php");
require_once("classes/HttpMethod.php");
require_once("classes/ElementProperty.php");
require_once("classes/ElementProperties.php");
require_once("classes/ElementsLoader.php");
require_once("classes/Debugger.php");
require_once("classes/Element.php");

// ------- Element Types ---------------
require_once("classes/TableCell.php");
require_once("classes/TableRow.php");
require_once("classes/TableHead.php");
require_once("classes/Table.php");
require_once("classes/Script.php");
require_once("classes/Head.php");
require_once("classes/Style.php");
require_once("classes/Image.php");
// -------------------------------------

require_once("classes/Language.php");
require_once("classes/WebPage.php");

Framework::SetDebug(true);
//Framework::CreateTables();
ob_start("ob_gzhandler");


?>