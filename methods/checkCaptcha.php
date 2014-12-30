<?php

session_start();

if ( isset($_GET["var"]) )
{
	$varName = $_GET["var"];
	if ( isset($_SESSION[$varName]) )
	{
		if ( isset($_GET["code"]) )	
		{
			if ( in_array($_GET["code"],$_SESSION[$varName]))
			{
				echo "code_correct";
			}
			else
			{
				echo "code_incorrect";
			}
		}	
		else 
		{
			echo "code_notSet";
		}	
	}
	else
	{
		echo "securityCode_notSet";
	}
}
else
{
	"varName_notSet";
}