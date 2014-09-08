<?php

session_start();

if ( isset($_SESSION["twork_securityCode"]) )
{
	if ( isset($_POST["code"]) )	
	{
		if ( $_SESSION["twork_securityCode"] == ($_POST["code"] ))
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