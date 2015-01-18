<?php

class ElementProperties
{
	private $elementProperties;

	public function AddProperty($addProperty)
	{
		for ( $i = 0 ; $i < count($this->elementProperties) ; $i++ )
		{
			$property = $this->elementProperties[$i];
			if ( $property->GetName() == $addProperty->GetName() )
			{
				$property->AddValue($addProperty->GetValue());
				return;
			}
		}
		array_push($this->elementProperties,$addProperty);
	}
	
	public function RemoveProperty($removeProperty)
	{
		for ( $i = 0 ; $i < count($this->elementProperties) ; $i++ )
		{
			$property = $this->elementProperties[$i];
			if ( $property->GetName() == $removeProperty->GetName() )
			{
				array_splice($this->elementProperties,$i);
				return;
			}
		}
	}
	
	public function RemovePropertyByName($propertyName)
	{
		$this->RemoveProperty($this->GetProperty($propertyName));
	}
	
	public function GetProperty($propertyName)
	{
		for ( $i = 0 ; $i < count($this->elementProperties) ; $i++ )
		{
			$property = $this->elementProperties[$i];
			if ( $property->GetName() == $propertyName )
			{
				return $property;
			}
		}
		// Property not found
		return new ElementProperty($propertyName,array());
	}
	
	public function GetProperties()
	{
		return $this->elementProperties;
	}
	
	public function __construct($propertiesArray=array())
	{
		$this->elementProperties = array();		
		$propertiesArrayKeys = array_keys($propertiesArray);
		foreach ( $propertiesArrayKeys as $propertyKey )
		{
			$property = new ElementProperty($propertyKey,$propertiesArray[$propertyKey]);
			$this->AddProperty($property);
		}
	}
}

?>