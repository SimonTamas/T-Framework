<?php

class Table extends Element
{
	private $tableRows;

	public function AddTableRow($row)
	{
		array_push($this->tableRows,$row);
		parent::AddElement($row);
		return $row;
	}
	

	public function __construct($properties=array())
	{
		$this->tableRows = array();
		parent::__construct("table",$properties);
	}
}

?>