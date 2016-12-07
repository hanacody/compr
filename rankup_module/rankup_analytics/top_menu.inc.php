<?php
/**
 * 구글로그분석 관리
 */
switch(basename($_SERVER['PHP_SELF'])) {
	case 'dimensions.html': $_key = 0; break;
	case 'pages.html': $_key = 1; break;
	case 'systems.html': $_key = 2; break;
	case 'index.html': $_key = 3; break;
}
$_sub_style[$_key] = ' style="font-weight:bold"';
$_title_texts = array('접속통계', '페이지통계', '시스템통계', '환경설정');
$rankup_control->print_admin_head('구글로그분석관리 -'.$_title_texts[$_key]);
?>
<style>
#topmenu{position:relative;background-color:#eeeeee;border-bottom:1px solid #dddddd;padding:15px 0px 10px 0px;text-align:center !important;}
#topmenu ul{display:inline;margin:0;margin-left:0 auto;margin-right:auto;padding:0;width:780px;text-align:left;}
#topmenu li{display:inline;background:url('../../Libs/_images/ic_arrow1.gif') no-repeat 0 35%;padding:0 0 0 10px;margin:0 7px;height:20px;line-height:200%;}
#topmenu .menu2 li{display:inline;background:url('../../Libs/_images/ic_arrow1.gif') no-repeat 0 35%;padding:0 0 0 10px;margin:0 5px;height:20px;line-height:200%;}
#topmenu li a{color:#314194;text-decoration:none;}
#topmenu li a:hover{text-decoration:underline;}
#topmenu li.selected{font-weight:bold}
</style>
<body>
<script type="text/javascript">
//2012-03-22 add
var log_reset = function() {
	if(!confirm('접속통계를 초기화 하시겠습니까?')) return false;
	proc.parameters({mode:'log_reset'});
	proc.process(function(trans) {
		if(trans.responseText.blank()) {
			alert('접속통계를 초기화 하였습니다.');
			document.location.reload();
		}
		else {
			alert(trans.responseText);
		}
	});
}
</script>
<div class="bar"><script> titleBar('구글로그분석관리 - <?=$_title_texts[$_key]?>', 400) </script></div>
<div id="topmenu" style="margin-bottom:30px;">
	<ul>
		<li><a href="./dimensions.html"<?=$_sub_style[0]?>>접속통계</a></li>
		<li><a href="./pages.html"<?=$_sub_style[1]?>>페이지통계</a></li>
		<li><a href="./systems.html"<?=$_sub_style[2]?>>시스템통계</a></li>
		<li><a href="./index.html"<?=$_sub_style[3]?>>환경설정</a></li>
		<li><a onclick="log_reset();">접속통계 초기화</a></li>
	</ul>
</div>
