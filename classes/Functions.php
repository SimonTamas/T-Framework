<?php

class Functions extends Framework
{
	public static function CreateTableFromResult($result,$canEdit)
	{
		$newTable = new Table(array( "class" => "pad" ));
		$headRow = new TableRow();
		for ( $h = 0 ; $h < mysql_num_fields($result) ; $h++ )
		{
			$newHead = new TableHead(mysql_field_name($result,$h),array( "class" => "pad" ));
			$headRow->AddTableCell($newHead);
		}
		if ( $canEdit )
		{
			$newHead = new TableHead(local_admin_edit,array( "class" => "pad" ));
			$headRow->AddTableCell($newHead);
		}
		$newTable->AddElement($headRow);
		for ( $i = 0 ; $i < mysql_num_rows($result) ; $i++ )
		{
			$newRow = new TableRow();
			$newTable->AddTableRow($newRow);
			for ( $c = 0 ; $c < mysql_num_fields($result); $c++ )
			{
				$newCell = new TableCell(array( "class" => "pad" ));
				$newCell->SetHTML(mysql_result($result,$i,$c));
				$newRow->AddTableCell($newCell);
			}
			if ( $canEdit )
			{
				$newCell = new TableCell(array( "class" => "pad" ));
				$editAnchor = new Element("a",array( "href" => $canEdit . "&id=" . $i ),local_admin_edit);
				$newCell->AddElement($editAnchor);
				$newRow->AddTableCell($newCell);
			}
		}
		if ( $canEdit )
		{
			$addRow = new TableRow();
			
			$addCell = new TableCell(array( "class" => "pad" ));
			$newAnchor = new Element("a",array( "href" => $canEdit . "&new" ),local_admin_new);
			$addCell->AddElement($newAnchor);
			
			$addRow->AddTableCell($addCell);
			$newTable->AddTableRow($addRow);
		}
		return $newTable;
	}
}


?>