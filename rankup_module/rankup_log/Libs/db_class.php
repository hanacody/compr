<?php
$_path = realpath("../../Libs/_php");
$_path = empty($_path) ? "./Libs/_php" : "../../Libs/_php";
include_once $_path."/rankup_connection.class.php";

# �迭�� ���� ������ �޾� sql �Է°����� ���� ���� 
# ���� ����ǥ�� �����ִ� ���
function setQuotation(&$val,$key) {
    $val="'".$val."'";      //������ ���� �����̹Ƿ� ���� �ٲ��.
}

class db_class {

	var $connect;
	function db_class() {
		$rankup_connection = new rankup_connection();
		$this->connect = $rankup_connection->connection();
	}

    # �� ����� $this->dbSelect()�ÿ��� ����.
    # $DB ��� �̸����� ��ü�� �����Ͽ��ٴ� �����Ͽ�,
    # ��Ÿ ����(select�� �̿�)�� ���ϰ��� ���ʿ��ϹǷ� 
    # mysql_query($query,$DB->connect) �� ����Ͻÿ�.
	function setResult($que) {
        //���ڵ� ���ҽ��� ����.
		$result=@mysql_query($que,$this->connect);
		   if(!$result) $er = $this->err($que); 
        //ȹ���� ���ε���� ����
		$cnt=@mysql_affected_rows();
		$re=array("cnt"=>$cnt,"result"=>$result);
		return $re;
	}

    # select��
	function sel($table,$where="",$field="*") {
		$q="select $field from $table $where";
		$re=$this->setResult($q); 
		return $re;
	}
    
    # single-insert
	function ins($table,$arr)  {            //$arr : �����迭
        array_walk($arr,"setQuotation",1);      //���� �����̼Ŵ�
        
        //���ڹ迭�� Ű���� �Է��׸����� �����ϰ�,
        //�����̼Ŵ׵� �Է��׸���� ���� ��ǥ�� �����Ͽ� �����Ѵ�.
		$q="insert into ".$table."(".implode(",",array_keys($arr)).") values(".implode(",",$arr).")";
        $re = mysql_query($q,$this->connect);
	    return $re;
	}
    
    # multi-insert
    function inm($table,$fields,$array) {
        //ù° ������ ���̺���
        //��° ������ �Է��ʵ���� �������� sql�� �ۼ��Ѵ�.
        $q="insert into $table($fields) values ";
        
        //count()�� ������ ������ ���Ѵ�.
        //�����ȿ� �� ��� �Ź� �Լ��� ����Ǿ� ������� �߻�
        $len=count($array);
        
        //�Էµ� ���ڵ���� chunk �� $datas �� �迭�� �ӽ�����
        for ($i=0;$i<$len;$i++) {
            array_walk($array[$i],"setQuotation");
            $datas[]="(".implode(",",$array[$i]).")";
        }
        
        //���ۼ��� sql ������ �Է·��ڵ� chunk �� ���δ�.
        $q.=implode(",",$datas);
        
        $re = mysql_query($q,$this->connect);
		return $re;
    }
    
    # update ��
	function upd($table,$arr,$where="") { //�����迭 $arr
        array_walk($arr,"setQuotation");        //�����̼Ŵ� ó���ϰ�
        
        //key=value ���� �Է°� �ӽù迭 ����
        $temps=array();
        while(list($key,$val)=each($arr)) {
            $temps[]="$key=$val";
        }
        
        //$temps�� ����� update���� �迭�� ","�� ��� ����.
		$q="update $table set ".implode(",",$temps)." $where";
        
        $re = mysql_query($q,$this->connect);
		return $re;
	}
    
    # delete ��
	function del($table,$where="") {
		$q="delete from $table $where";		
        $re = mysql_query($q,$this->connect);
		return $re;
	}
    
	# db ��������
	function err($que){
		$err_no = mysql_errno();
		$err_msg = mysql_error();
		$error_msg = "ERROR CODE " . $err_no . " : " . $err_msg;
		$error_msg = addslashes($error_msg);
		echo "<table border=0 cellpadding=3 cellspacing=1>\n";
		echo "<tr>\n";
		echo "<td colspan=2 align=center bgcolor=#cccccc><b><font size=2>�������� ������ �����ϴ�.</b></font></td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td bgcolor=#F5F5F5><font size=2>������</font></td>\n";
		echo "<td><font size=2>$que</font></td>\n";
		echo "</tr>\n";		
		echo "<tr><td colspan=2 height=1 bgcolor=#CCCCCC></td></tr>\n";
		echo "<tr>\n";
		echo "<td bgcolor=#F5F5F5><font size=2>�����޼���</font></td>\n";
		echo "<td><font size=2>$error_msg</font></td>\n";
		echo "</tr>\n";
		echo "<tr><td colspan=2 height=1 bgcolor=#CCCCCC></td></tr>\n";
		echo "<td bgcolor=#F5F5F5><font size=2>������</font></td>\n";
		echo "<td><font size=2>$_SERVER[PHP_SELF]</font></td>\n";
		echo "</tr>\n";
		
		echo "<tr><td colspan=2 height=1 bgcolor=#CCCCCC></td></tr>\n";
		echo "</table>\n";
		exit;
	}

    # ó���� �̵��� �ּ�
    function t_page($msg,$url) {
         echo" <script> alert('$msg'); location.replace('$url'); </script> ";
    }

    function f_page($msg) {
         echo" <script> alert('$msg'); history.go(-1); </script> ";
    }
}
?>
