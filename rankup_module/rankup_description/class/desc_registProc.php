<?PHP
#start
require_once "../../../Libs/_php/rankup_basic.class.php";
require_once "../config.inc.php";

$_VAR = $_POST ? $_POST : $_GET;

if($_VAR['mode']=="delete") {
	if($_VAR['idx']) {
		$prev_class = $rankup_control->queryFetch("select location, object from $table_name where kind='C' and idx=$_VAR[idx]");
		// Ŭ������ �����ϴ� ��� - ���� �޼ҵ� ������ �Բ� ����
		if(empty($prev_class['object'])) {
			$rankup_control->query("delete from $table_name where kind='C' and location='$prev_class[location]' and object<>''");
			$code = "parent.description.getClassList();"; // Ŭ���� ����Ʈ ����
		}
		else {
			// �޼ҵ� â ����
			$code = "parent.description.getClassInfo(\"$prev_class[location]\",'');";
		}
		// ������ �׸� ����
		$rankup_control->query("delete from $table_name where idx=$_VAR[idx]");
		$rankup_control->query("optimize table $table_name");
		echo "
		<script type='text/javascript'>
		<!--
		alert('���������� �����Ǿ����ϴ�.          ');
		$code
		//-->
		</script>";
	}
	else {
		echo "
		<script type='text/javascript'>
		<!--
		alert('������ �׸��� ���õ��� �ʾҽ��ϴ�.          ');
		//-->
		</script>";
	}
	exit;
}

## fobject �� description ���� ������ �űԵ��, ������ ����
## �Ѱܹ��� ���߿� description ���� ������ �ش� ���ڵ� ����
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

	// ������
	if($_VAR['idx']) {
		// Ŭ���� ����� ���
		if(empty($_VAR['fmethod'])) {
			// �ߺ��Ǵ� Ŭ������(location) �� �Է��� �� ���� ����! ������ ������ �߻��ϰ� �ȴ�.
			$prev_class = $rankup_control->queryFetch("select idx, location from $table_name where kind='C' and location='$fclass' and object=''");
			if(!empty($prev_class) && $prev_class['idx']!=$_VAR['idx']) {
				echo "
				<script type='text/javascript'>
				<!--
				//parent.document.getElementById('classRegistFrmDiv').style.display='none';
				alert(\"�˼��մϴ�. �̹� �����ϴ� Ŭ�����̸��Դϴ�. Ŭ�����̸��� �ߺ����� �ʵ��� �Է��Ͽ� �ֽʽÿ�.          \");
				parent.document.getElementsByName('classRegistFrm')[0].fclass.focus();
				parent.document.getElementsByName('classRegistFrm')[0].fclass.select();
				//-->
				</script>";
				exit;
			}
			// Ŭ�������� �ٲ��� ���� Ŭ���������� ��ϵǾ� �ִ� �޼ҵ� ���� Ŭ������(location)�� ��� �ٲ� �ش�.
			$prev_location = $rankup_control->queryR("select location from $table_name where idx=$_POST[idx]");
			if(!empty($prev_location)) {
				$rankup_control->query("update $table_name set location='$fclass' where kind='C' and location='$prev_location' and object<>''");
			}
			// ������Ʈ
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
		// �޼ҵ� ����� ���
		else {
			// ������Ʈ
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
	// �űԵ��
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