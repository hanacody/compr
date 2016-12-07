<?php
$_path = realpath("../../Libs/_php");
$_path = empty($_path) ? "./Libs/_php" : "../../Libs/_php";
include_once $_path."/rankup_connection.class.php";

# 배열의 값을 참조로 받아 sql 입력값으로 쓰기 위해 
# 작은 따옴표를 씌워주는 기능
function setQuotation(&$val,$key) {
    $val="'".$val."'";      //참조에 의한 전달이므로 값이 바뀐다.
}

class db_class {

	var $connect;
	function db_class() {
		$rankup_connection = new rankup_connection();
		$this->connect = $rankup_connection->connection();
	}

    # 본 펑션은 $this->dbSelect()시에만 사용됨.
    # $DB 라는 이름으로 객체를 생성하였다는 전제하에,
    # 기타 쿼리(select문 이외)는 리턴값이 불필요하므로 
    # mysql_query($query,$DB->connect) 로 사용하시오.
	function setResult($que) {
        //레코드 리소스를 세팅.
		$result=@mysql_query($que,$this->connect);
		   if(!$result) $er = $this->err($que); 
        //획득한 레로드수를 세팅
		$cnt=@mysql_affected_rows();
		$re=array("cnt"=>$cnt,"result"=>$result);
		return $re;
	}

    # select문
	function sel($table,$where="",$field="*") {
		$q="select $field from $table $where";
		$re=$this->setResult($q); 
		return $re;
	}
    
    # single-insert
	function ins($table,$arr)  {            //$arr : 연관배열
        array_walk($arr,"setQuotation",1);      //값을 쿼테이셔닝
        
        //인자배열의 키들을 입력항목으로 열거하고,
        //쿼테이셔닝된 입력항목들의 값을 쉼표로 구분하여 열거한다.
		$q="insert into ".$table."(".implode(",",array_keys($arr)).") values(".implode(",",$arr).")";
        $re = mysql_query($q,$this->connect);
	    return $re;
	}
    
    # multi-insert
    function inm($table,$fields,$array) {
        //첫째 인자인 테이블명과
        //둘째 인자인 입력필드명을 구조로한 sql을 작성한다.
        $q="insert into $table($fields) values ";
        
        //count()로 인자의 개수를 구한다.
        //루프안에 둘 경우 매번 함수가 실행되어 오버헤드 발생
        $len=count($array);
        
        //입력될 레코드들의 chunk 를 $datas 에 배열로 임시저장
        for ($i=0;$i<$len;$i++) {
            array_walk($array[$i],"setQuotation");
            $datas[]="(".implode(",",$array[$i]).")";
        }
        
        //기작성된 sql 쿼리에 입력레코드 chunk 를 붙인다.
        $q.=implode(",",$datas);
        
        $re = mysql_query($q,$this->connect);
		return $re;
    }
    
    # update 문
	function upd($table,$arr,$where="") { //연관배열 $arr
        array_walk($arr,"setQuotation");        //쿼테이셔닝 처리하고
        
        //key=value 쌍의 입력값 임시배열 정의
        $temps=array();
        while(list($key,$val)=each($arr)) {
            $temps[]="$key=$val";
        }
        
        //$temps에 저장된 update구조 배열을 ","로 묶어서 리턴.
		$q="update $table set ".implode(",",$temps)." $where";
        
        $re = mysql_query($q,$this->connect);
		return $re;
	}
    
    # delete 문
	function del($table,$where="") {
		$q="delete from $table $where";		
        $re = mysql_query($q,$this->connect);
		return $re;
	}
    
	# db 에러문구
	function err($que){
		$err_no = mysql_errno();
		$err_msg = mysql_error();
		$error_msg = "ERROR CODE " . $err_no . " : " . $err_msg;
		$error_msg = addslashes($error_msg);
		echo "<table border=0 cellpadding=3 cellspacing=1>\n";
		echo "<tr>\n";
		echo "<td colspan=2 align=center bgcolor=#cccccc><b><font size=2>쿼리에서 에러가 났습니다.</b></font></td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td bgcolor=#F5F5F5><font size=2>쿼리문</font></td>\n";
		echo "<td><font size=2>$que</font></td>\n";
		echo "</tr>\n";		
		echo "<tr><td colspan=2 height=1 bgcolor=#CCCCCC></td></tr>\n";
		echo "<tr>\n";
		echo "<td bgcolor=#F5F5F5><font size=2>에러메세지</font></td>\n";
		echo "<td><font size=2>$error_msg</font></td>\n";
		echo "</tr>\n";
		echo "<tr><td colspan=2 height=1 bgcolor=#CCCCCC></td></tr>\n";
		echo "<td bgcolor=#F5F5F5><font size=2>페이지</font></td>\n";
		echo "<td><font size=2>$_SERVER[PHP_SELF]</font></td>\n";
		echo "</tr>\n";
		
		echo "<tr><td colspan=2 height=1 bgcolor=#CCCCCC></td></tr>\n";
		echo "</table>\n";
		exit;
	}

    # 처리후 이동할 주소
    function t_page($msg,$url) {
         echo" <script> alert('$msg'); location.replace('$url'); </script> ";
    }

    function f_page($msg) {
         echo" <script> alert('$msg'); history.go(-1); </script> ";
    }
}
?>
