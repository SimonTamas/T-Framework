<?php

class ElementProperty
{
	private $propertyName;	
	private $propertyValues;
	
	public function GetHTML()
	{
		return " " . $this->GetName() . "='" . $this->GetValue(true) . "'";
	}
	
	public function GetName()
	{
		return $this->propertyName;
	}
	
	public function GetValue($obfuscate=false)
	{
		$propertyValues = "";
		$valuesCount = count($this->propertyValues);
		for ( $i = 0 ; $i < $valuesCount ; $i++ )
		{
			if ( $obfuscate && ($this->GetName() == "id" || $this->GetName() == "class") )
			{
				$propertyValues .= Obfuscator::GetKey($this->propertyValues[$i],$this->GetName());
			}
			else
			{
				$propertyValues .= $this->propertyValues[$i];
			}
			if ( $i+1 < $valuesCount )
			{
				$propertyValues .= " ";
			}
		}
		return $propertyValues;
	}
	
	public function AddValue($value)
	{
		if ( !in_array($value,$this->propertyValues) )
		{
			array_push($this->propertyValues,$value);
			return;
		}
		// Add DEBUG already found value !
	}
	
	public function RemoveValue($value)
	{
		foreach ( $propertyKeys as $propertyKey )
		{
			if ( $this->propertyValues[$i] == $value )
			{
				array_splice($this->propertyValues,$i);
				return;
			}
		}
		//$this->debugger->Log(eng_VALUE_NOT_FOUND . " => " . $value . " <= "  eng_value_not_found_inside . " : " . $propertyName );
	}

	public function __construct($propertyName,$defaultValues=array())
	{
		$this->propertyName = $propertyName;
		$this->propertyValues = array();
		
		if ( !is_array($defaultValues) )
		{
			$defaultValues = array($defaultValues);
		}
		for ( $i = 0 ; $i < count($defaultValues) ; $i++ )
		{
			$this->AddValue($defaultValues[$i]);
		}
	}
}

?>