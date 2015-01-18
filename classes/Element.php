<?php

class Element
{	
	const startTag = "<";
	const startEndTag = "</";
	const closeTag = ">";
	const endTag = "/>";
	
	private $elementTypeString;
	private $elementStartHTML;
		
	private $elementChildren;
	private $elementProperties;
	
	public function Html()
	{
		return $this->elementStartHTML;
	}
	
	public function SetHTML($setTo)
	{
		$this->elementStartHTML = $setTo;
	}
	
	public function AppendHTML($append)
	{
		$this->elementStartHTML .= $append;
	}
			
	public function Type()
	{
		return $this->elementTypeString;
	}
	
	public function ElementProperties()
	{
		return $this->elementProperties;
	}
	
	public function AddProperty($property)
	{
		$this->elementProperties->AddProperty($property);
	}
	
	public function GetChildElement($type,$id="")
	{
		for ( $i = 0 ; $i < count($this->elementChildren) ; $i++ )
		{
			$child = $this->elementChildren[$i];
			if ( $child->Type() == $type && ( (strlen($id) > 0  && $child->elementProperties->GetProperty("id")->GetValue() == $id) || strlen($id) == 0 ) )
			{
				return $child;
			}
		}		
	}
	
	public function GetChildElements($type,$id="",$returnArray=array())
	{
		
		for ( $i = 0 ; $i < count($this->elementChildren) ; $i++ )
		{
			$child = $this->elementChildren[$i];
			if ( $child->Type() == $type && ( (strlen($id) > 0  && $child->elementProperties->GetProperty("id")->GetValue() == $id) || strlen($id) == 0 ) )
			{
				array_push($returnArray,$child);
			}
			else
			{
				$foundElements = $child->GetChildElements($type,$id,$returnArray);
				if ( $foundElements ) 
				{
					for ( $c = 0 ; $c < count($foundElements) ; $c++ )
					{
						array_push($returnArray,$foundElements[$c]);
					}	
				} 
			}
		}
		return $returnArray;
	}
	
	public function FindChildElement($type,$id="",$propagating=false)
	{
		for ( $i = 0 ; $i < count($this->elementChildren) ; $i++ )
		{
			$child = $this->elementChildren[$i];
			if ( $child->Type() == $type && ( (strlen($id) > 0  && $child->elementProperties->GetProperty("id")->GetValue() == $id) || strlen($id) == 0 ) )
			{
				return $child;
			}
			else
			{
				$propagate = $child->FindChildElement($type,$id,true);
				if ( $propagate ) 
				{
					return $propagate;
				} 
			}
		}
		// If not child element was found lets return an empty Element
		if ( $propagating )
		{
			return false;
		}
		return new Element("");
	}
		
	
	public function GetHTML($obfuscate=false)
	{ 	
		$elementHTML = $this->Html();
		for ( $i = 0 ; $i < count($this->elementChildren) ; $i++ )
		{
			$child = $this->elementChildren[$i];
			$elementHTML .= $child->GetHTML($obfuscate);
		}
 		return $this->GetElementStartTag($obfuscate) . $elementHTML . $this->GetElementEndTag();
	}
	
	public function GetElementProperties($obfuscate=false)
	{
		$elementPropertiesString = "";
		if ( $this->elementProperties )
		{
			$elementProperties = $this->elementProperties->GetProperties();
			foreach ( $elementProperties as $elementProperty )
			{
				// If value isn't null
				if ( strlen($elementProperty->GetValue()) > 0 )
				{
					$elementPropertiesString .= $elementProperty->GetHTML($obfuscate);
				}
			}
		}
		return $elementPropertiesString;
	}
	
	public function GetPropertyValue($prop)
	{
		return $this->elementProperties->GetProperty($prop)->GetValue();
	}
	
	public function IsVoidElement()
	{
		$voidElements = array("area","base","br","col","command","embed","hr","img","input","link","meta","param","source");
		return in_array($this->Type(),$voidElements);
	}

		
	public function GetElementStartTag($obfuscate=false)
	{
		if ( $this->IsEmptyElement() )
		{
			return "";
		}
		else if ( $this->IsVoidElement() )
		{
			return self::startTag . $this->Type() . $this->GetElementProperties($obfuscate) . self::endTag;
		}
		else
		{
			return self::startTag . $this->Type() . $this->GetElementProperties($obfuscate) . self::closeTag;	
		}
	}
	
	
	public function GetElementEndTag()
	{
		if ( $this->IsEmptyElement() )
		{
			return "";
		}
		else if ( ! $this->IsVoidElement() )
		{
			return self::startEndTag . $this->Type(). self::closeTag;
		}
	}
	
	public function IsEmptyElement()
	{
		return $this->elementTypeString == "";
	}
	
	public function AddElement($element)
	{
		array_push($this->elementChildren,$element);
	}
	
	public function PrependElement($element)
	{
		array_unshift($this->elementChildren,$element);
	}
	
	public function AddElements($elementsArray)
	{
		for ( $i = 0 ; $i < count($elementsArray) ; $i++ )
		{
			$this->AddElement($elementsArray[$i]);
		}
	}
	
	function __construct($elementTypeString,$propertiesArray=array(),$elementStartHTML="") 
	{
		$this->elementTypeString = $elementTypeString;
		$this->elementStartHTML = $elementStartHTML; 
				
		$this->elementChildren = array();
		$this->elementProperties = new ElementProperties($propertiesArray);
   	}
}

?>