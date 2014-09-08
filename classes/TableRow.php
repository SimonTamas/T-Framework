<?php

class TableRow extends Element
{
	private $rowCells;

	public function AddTableCell($cell)
	{
		array_push($this->rowCells,$cell);
		parent::AddElement($cell);
	}

	public function __construct($properties=array())
	{
		$this->rowCells = array();
		parent::__construct("tr",$properties);
	}
}

?>