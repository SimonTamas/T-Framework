<?php

class TableHead extends Element
{
	private $headCells;

	public function AddTableCell($cell)
	{
		array_push($this->headCells,$cell);
		parent::AddElement($cell);
	}

	public function __construct($defHTML,$properties=array())
	{
		$this->headCells = array();
		parent::__construct("th",$properties,$defHTML);
	}
}

?>