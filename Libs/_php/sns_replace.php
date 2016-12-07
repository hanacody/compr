<?php
include_once "rankup_basic.class.php";
include_once "rankup_sns.class.php";
include_once $base_dir."rankup_module/rankup_board/rankup_board.class.php"; // 2011.01.24 added
$rankup_board = new rankup_board(empty($_GET['id']) ? $_POST['id'] : $_GET['id']);
$rankup_sns = new rankup_sns;

if($ftype==1) $rankup_board = new rankup_board(empty($_GET['id']) ? $_POST['id'] : $_GET['id']); //게시판일때만

$replace_url = $rankup_sns->sns_link_replace($_GET);
$rankup_control->print_user_head("SNS 페이지 이동");
?>
<body>
<?php
if($_GET['stype'] == "linknow") { ?>
<script type="text/javascript">
	var data = encodeURIComponent('{"title":"<?=$replace_url[subject]?>","url":"<?=$replace_url[url]?>"}');
	window.open('http://www.linknow.kr/?go=send_wall&data='+data);
	self.close();
</script>
<? } else { ?>
<meta http-equiv="refresh" content="0;url=<?=$replace_url?>">
<? } ?>
</body>
</html>