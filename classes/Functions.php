<?php

class Functions extends Framework
{
	public static function CreateTableFromResult($result,$canEdit)
	{
		$newTable = new Table(array( "class" => "pad" ));
		$headRow = new TableRow();
		for ( $h = 0 ; $h < $result->field_count ; $h++ )
		{
			$fieldName = mysqli_fetch_field($result)->name;
			$newHead = new TableHead($fieldName,array( "class" => "pad" ));
			$headRow->AddTableCell($newHead);
		}
		if ( $canEdit )
		{
			$newHead = new TableHead(local_admin_edit,array( "class" => "pad" ));
			$headRow->AddTableCell($newHead);
		}
		$newTable->AddElement($headRow);
		$resultArray = SqlServer::ResultArray($result);
		for ( $i = 0 ; $i < count($resultArray) ; $i++ )
		{
			$rowArray = $resultArray[$i];
			$newRow = new TableRow();
			$newTable->AddTableRow($newRow);
			for ( $c = 0 ; $c < count($rowArray) ; $c++ )
			{
				$newCell = new TableCell(array( "class" => "pad" ));
				$newCell->SetHTML($resultArray[$i][$c]);
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