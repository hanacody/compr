<?PHP
#start
require_once "../../../Libs/_php/rankup_basic.class.php";
require_once "../config.inc.php";

$_VAR = $_POST ? $_POST : $_GET;

if($_VAR['mode']=="delete") {
	if($_VAR['idx']) {
		$prev_class = $rankup_control->queryFetch("select location, object from $table_name where kind='C' and idx=$_VAR[idx]");
		// 클래스를 삭제하는 경우 - 관련 메소드 정보도 함께 삭제
		if(empty($prev_class['object'])) {
			$rankup_control->query("delete from $table_name where kind='C' and location='$prev_class[location]' and object<>''");
			$code = "parent.description.getClassList();"; // 클래스 리스트 갱신
		}
		else {
			// 메소드 창 갱신
			$code = "parent.description.getClassInfo(\"$prev_class[location]\",'');";
		}
		// 선택한 항목 삭제
		$rankup_control->query("delete from $table_name where idx=$_VAR[idx]");
		$rankup_control->query("optimize table $table_name");
		echo "
		<script type='text/javascript'>
		<!--
		alert('정상적으로 삭제되었습니다.          ');
		$code
		//-->
		</script>";
	}
	else {
		echo "
		<script type='text/javascript'>
		<!--
		alert('삭제할 항목이 선택되지 않았습니다.          ');
		//-->
		</script>";
	}
	exit;
}

## fobject 에 description 값이 없으면 신규등록, 있으면 수정
## 넘겨받은 값중에 description 값이 없으면 해당 레코드 삭제
if(empty($_VAR['fclass'])) {
	echo "
	<script type='text/javascript'>
	<!--
	parent.document.getElementById('classRegistFrmDiv').style.display = 'none';
	//-->
	</script>";
	exit;
}
else {
	$fclass = str_replace("'", "''", $_VAR['fclass']);
	$fmethod = str_replace("'", "''", $_VAR['fmethod']);
	$description = str_replace("'", "''", $_VAR['description']);

	// 수정시
	if($_VAR['idx']) {
		// 클래스 등록일 경우
		if(empty($_VAR['fmethod'])) {
			// 중복되는 클래스명(location) 을 입력할 수 없게 하자! 삭제시 문제가 발생하게 된다.
			$prev_class = $rankup_control->queryFetch("select idx, location from $table_name where kind='C' and location='$fclass' and object=''");
			if(!empty($prev_class) && $prev_class['idx']!=$_VAR['idx']) {
				echo "
				<script type='text/javascript'>
				<!--
				//parent.document.getElementById('classRegistFrmDiv').style.display='none';
				alert(\"죄송합니다. 이미 존재하는 클래스이름입니다. 클래스이름이 중복되지 않도록 입력하여 주십시오.          \");
				parent.document.getElementsByName('classRegistFrm')[0].fclass.focus();
				parent.document.getElementsByName('classRegistFrm')[0].fclass.select();
				//-->
				</script>";
				exit;
			}
			// 클래스명이 바뀐경우 기존 클래스명으로 등록되어 있는 메소드 들의 클래스명(location)을 모두 바꿔 준다.
			$prev_location = $rankup_control->queryR("select location from $table_name where idx=$_POST[idx]");
			if(!empty($prev_location)) {
				$rankup_control->query("update $table_name set location='$fclass' where kind='C' and location='$prev_location' and object<>''");
			}
			// 업데이트
			$rankup_control->query("update $table_name set location='$fclass', description='$description' where idx=$_VAR[idx]");
			echo "
			<script type='text/javascript'>
			<!--
			try {
				parent.document.getElementById('classRegistFrmDiv').style.display='none';
				parent.document.getElementById('fclass_id$_VAR[idx]').innerHTML = \"$_VAR[fclass]\";
				parent.document.getElementById('description_id$_VAR[idx]').innerHTML = \"$_VAR[description]\";
			}
			catch(e) {
				// do nothing
				//alert(e.message);
			}
			//-->
			</script>";
		}
		// 메소드 등록일 경우
		else {
			// 업데이트
			$rankup_control->query("update $table_name set object='$fmethod', description='$description' where idx=$_VAR[idx]");
			echo "
			<script type='text/javascript'>
			<!--
			try {
				parent.document.getElementById('methodRegistFrmDiv').style.display='none';
				parent.document.getElementById('fmethod_id$_VAR[idx]').innerHTML = \"$_VAR[fmethod]\";
				parent.document.getElementById('description_id$_VAR[idx]').innerHTML = \"".str_replace("\r\n", "<br>", $_VAR['description'])."\";
			}
			catch(e) {
				// do nothing
				//alert(e.message);
			}
			//-->
			</script>";
		}
	}
	// 신규등록
	else {
		$rankup_control->query("insert into $table_name set location='$fclass', object='$fmethod', kind='C', description='$description'");
		if(empty($_VAR['fmethod'])) {
			echo "
			<script type='text/javascript'>
			<!--
			parent.document.getElementById('classRegistFrmDiv').style.display='none';
			parent.description.getClassList();
			//-->
			</script>";
		}
		else {
			echo "
			<script type='text/javascript'>
			<!--
			parent.document.getElementById('methodRegistFrmDiv').style.display='none';
			parent.description.getClassInfo(\"$_VAR[fclass]\",'');
			//-->
			</script>";
		}
	}
}

#end
?>