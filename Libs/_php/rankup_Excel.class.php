<?php
class Excel
{
	var  $excel_data;
	var  $excel_filename;

	function  Excel($excel_filename='')
	{
		$this->excel_data			=	"";
		$this->excel_filename	=	$excel_filename;
		$this->ExcelStart();
	}

	function ExcelStart()
	{
		$this->excel_data = "<Table border='1' cellpadding='0' cellspacing='0'>";
	}

	function ExcelEnd()
	{
		$this->excel_data .= "</Table>";
	}

	function WriteText( $row, $col, $value )
	{
		$this->excel_data .= "<td>".$value."&nbsp;</td>";
	}

	function SendFile()
	{
		$this->ExcelEnd();
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');	// always modified
		header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
		header('Pragma: no-cache'); // HTTP/1.0
		header("Content-Type: application/vnd.ms-excel");
		header('Content-Disposition: attachment; filename='.$this->excel_filename.'.xls');
		//html 형식으로 지정한다.
		echo '<html>';
		echo '<head>';
		echo '<meta http-equiv="Content-Type" content="application/vnd.ms-excel;charset=euc-kr">';
		echo '</head>';
		echo '<body>';
		print $this->excel_data;
		echo '</body>';
		echo '</html>';
	}
}
?>