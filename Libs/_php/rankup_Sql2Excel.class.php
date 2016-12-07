<?php
include_once "rankup_Excel.class.php";

class Sql2Excel
{
	var $excelName = '';

	function Sql2Excel($q = '')
	{
		$this->excelName = $q;
	}

	function ExcelOutput($query = '')
	{
		$result			= mysql_query($query);
		$fieldNum		= mysql_num_fields($result);
		$fieldType	=	array();

		for ($i = 0; $i < $fieldNum; $i++){
			$fieldType[] = mysql_field_type($result,$i);
		}

		$excel = new Excel($this->excelName);

		$rowsCounter = 0;

		for($i = 0; $i < $fieldNum; $i++) {
			$fld			= mysql_fetch_field($result, $i);
			$fldname	=	$fld->name;
			$excel->WriteText($rowsCounter, $i, $fldname);
		}

		$rowsCounter++;

		while ($row = mysql_fetch_array($result)) {
			for ($colsCounter = 0; $colsCounter < $fieldNum; $colsCounter++) {
				if (eregi("(int)", $fieldType[$colsCounter])) {
					$excel->WriteNumber($rowsCounter, $colsCounter, $row[$colsCounter]);
				} else {
					$excel->WriteText($rowsCounter, $colsCounter, $row[$colsCounter]);
				}
			}
			$rowsCounter++;
		}
		$excel->SendFile();
		return;
	}

	//데이터 형태로 전환했을경우 [아이디]= "아이디"
	function ExcelOutputData($ExcelData)
	{
		$excel = new Excel($this->excelName);

		$rowsCounter = 0;

		foreach($ExcelData as $ExcelTitleKey => $ExcelTitleRows) {
			if($ExcelTitleKey == 0) {
				$cnt = 0;
				$excel->excel_data .= "<tr align='center' style='font-weight:bold'>";
				foreach($ExcelTitleRows as $key => $val) {
					$excel->WriteText($rowsCounter, $cnt, $key);
					$cnt++;
				}
				$excel->excel_data .= "</tr>";
			} else {
			break;
			}
		}
		$rowsCounter++;
		foreach($ExcelData as $ExcelRows) {
			$colsCounter = 0;
			$excel->excel_data .= "<tr>";
			foreach($ExcelRows as $ExcelValue) {
				$excel->WriteText($rowsCounter, $colsCounter, $ExcelValue);
				$colsCounter++;
			}
			$excel->excel_data .= "</tr>";
			$rowsCounter++;
		}
		$excel->SendFile();
		return;
	}

}
?>